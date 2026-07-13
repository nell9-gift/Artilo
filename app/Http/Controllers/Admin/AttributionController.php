<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Artisan;
use App\Models\MissionRefus;
use App\Jobs\AffecterArtisan;
use App\Notifications\MissionProposeeNotification;
use Illuminate\Http\Request;

class AttributionController extends Controller
{
    /**
     * Liste des demandes en attente d'attribution
     */
    public function index()
    {
        $demandesEnAttente = Mission::where('statut', 'en_attente')
            ->with(['particulier', 'metier'])
            ->latest()
            ->paginate(20);

        $missionsAffectees = Mission::where('statut', 'affectee')
            ->with(['particulier', 'artisan.user', 'metier'])
            ->latest()
            ->paginate(20);

        return view('admin.attributions.index', compact(
            'demandesEnAttente',
            'missionsAffectees'
        ));
    }

    /**
     * Affiche les 3 meilleurs artisans pour une mission (proposition)
     */
    public function show(Mission $mission)
    {
        abort_unless($mission->statut === 'en_attente', 422, 'Cette mission n\'est plus en attente.');

        $mission->load(['particulier', 'metier']);

        // === ALGORITHME DE MATCHING - TOP 3 ===
        $query = Artisan::query()
            ->where('status', 'approved')       // Uniquement les validés
            ->where('est_disponible', true)      // Disponibles
            ->with('user')
            ->withCount(['missions as missions_acceptees_count' => function ($q) {
                $q->whereIn('statut', ['acceptee', 'en_cours', 'payee', 'validee_client']);
            }]);

        // Filtrer par métier si la mission a un metier_id
        if ($mission->metier_id) {
            $query->where('metier_id', $mission->metier_id);
        }

        // Exclure les artisans qui ont déjà refusé cette mission
        $query->whereDoesntHave('missionsRefusees', function ($q) use ($mission) {
            $q->where('mission_id', $mission->id);
        });

        // Calcul de la distance si coordonnées disponibles
        if ($mission->latitude && $mission->longitude) {
            $query->selectRaw('artisans.*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance_km', [
                $mission->latitude,
                $mission->longitude,
                $mission->latitude
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($q) use ($mission) {
                $q->whereNull('max_distance_km')
                  ->orWhereRaw('(6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                  )) <= max_distance_km', [
                    $mission->latitude,
                    $mission->longitude,
                    $mission->latitude
                  ]);
            })
            ->orderBy('distance_km');
        } else {
            // Ajouter un select par défaut si pas de coordonnées
            $query->select('artisans.*');
        }

        // Trier par score interne puis note
        $query->orderByDesc('score_interne')
              ->orderByDesc('average_rating');

        $candidats = $query->take(3)->get();

        // Calculer un score de matching pour chaque candidat (0-100)
        foreach ($candidats as $candidat) {
            $score = 0;

            // Score basé sur le niveau (debutant=20, confirme=40, expert=60)
            $scoreNiveau = match ($candidat->niveau) {
                'expert' => 60,
                'confirme' => 40,
                'debutant' => 20,
                default => 30,
            };
            $score += $scoreNiveau;

            // Score basé sur l'évaluation (average_rating / 5 * 20)
            $scoreNote = min(20, ($candidat->average_rating ?? 0) / 5 * 20);
            $score += $scoreNote;

            // Score basé sur l'expérience (missions acceptées)
            $missionsCount = $candidat->missions_acceptees_count ?? 0;
            $scoreExperience = min(20, $missionsCount * 4);
            $score += $scoreExperience;

            // Distance (si disponible)
            if (isset($candidat->distance_km)) {
                if ($candidat->distance_km <= 5) {
                    $score += 20;
                } elseif ($candidat->distance_km <= 15) {
                    $score += 15;
                } elseif ($candidat->distance_km <= 30) {
                    $score += 10;
                } elseif ($candidat->distance_km <= 50) {
                    $score += 5;
                }
            } else {
                $score += 10; // Score neutre si pas de distance
            }

            $candidat->score_matching = min(100, $score);
        }

        // Trier par score matching (desc)
        $candidats = $candidats->sortByDesc('score_matching')->values();

        return view('admin.attributions.show', compact('mission', 'candidats'));
    }

    /**
     * Attribuer la mission à l'artisan sélectionné par l'admin
     */
    public function attribuer(Request $request, Mission $mission)
    {
        abort_unless($mission->statut === 'en_attente', 422, 'Cette mission n\'est plus en attente.');

        $request->validate([
            'artisan_id' => 'required|exists:artisans,id',
        ]);

        $artisan = Artisan::findOrFail($request->artisan_id);

        $mission->update([
            'artisan_id' => $artisan->id,
            'statut' => 'affectee',
            'affectee_le' => now(),
            'expire_le' => now()->addMinutes(config('artilo.delai_acceptation_minutes', 20)),
        ]);

        // Notifier le prestataire
        $artisan->user->notify(new MissionProposeeNotification($mission));

        return redirect()->route('admin.attributions.index')
            ->with('success', "Prestataire « {$artisan->nom} » attribué à la mission #{$mission->id}. Une notification lui a été envoyée.");
    }

    /**
     * Annuler l'attribution en cours
     */
    public function annuler(Mission $mission)
    {
        abort_unless($mission->statut === 'affectee', 422, 'Aucune attribution en cours.');

        $mission->update([
            'artisan_id' => null,
            'statut' => 'en_attente',
            'affectee_le' => null,
            'expire_le' => null,
        ]);

        return redirect()->route('admin.attributions.index')
            ->with('info', 'Attribution annulée pour la mission #' . $mission->id);
    }

    /**
     * Forcer l'attribution à un prestataire spécifique (recherche manuelle)
     */
    public function forcerAttribution(Request $request, Mission $mission)
    {
        abort_unless($mission->statut === 'en_attente', 422, 'Cette mission n\'est plus en attente.');

        $request->validate([
            'artisan_id' => 'required|exists:artisans,id',
        ]);

        $artisan = Artisan::findOrFail($request->artisan_id);

        $mission->update([
            'artisan_id' => $artisan->id,
            'statut' => 'affectee',
            'affectee_le' => now(),
            'expire_le' => now()->addMinutes(config('artilo.delai_acceptation_minutes', 20)),
        ]);

        // Notifier le prestataire
        $artisan->user->notify(new MissionProposeeNotification($mission));

        return redirect()->route('admin.attributions.index')
            ->with('success', "Prestataire « {$artisan->nom} » attribué manuellement à la mission #{$mission->id}.");
    }
}
