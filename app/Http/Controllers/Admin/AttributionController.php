<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Jobs\AffecterArtisan;
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
     * Attribuer manuellement un prestataire (déclencher la recherche)
     */
    public function attribuer(Mission $mission)
    {
        abort_unless($mission->statut === 'en_attente', 422, 'Cette mission n\'est plus en attente.');

        // Lancer la recherche automatique
        AffecterArtisan::dispatch($mission);

        return redirect()->route('admin.attributions.index')
            ->with('success', 'Recherche de prestataire lancée pour la mission #' . $mission->id);
    }

    /**
     * Voir les détails d'une mission
     */
    public function show(Mission $mission)
    {
        $mission->load(['particulier', 'artisan.user', 'metier', 'refus.artisan.user']);
        return view('admin.attributions.show', compact('mission'));
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
     * Forcer l'attribution à un prestataire spécifique
     */
    public function forcerAttribution(Request $request, Mission $mission)
    {
        abort_unless($mission->statut === 'en_attente', 422, 'Cette mission n\'est plus en attente.');

        $request->validate([
            'artisan_id' => 'required|exists:artisans,id',
        ]);

        $artisan = \App\Models\Artisan::find($request->artisan_id);

        $mission->update([
            'artisan_id' => $artisan->id,
            'statut' => 'affectee',
            'affectee_le' => now(),
            'expire_le' => now()->addMinutes(config('artilo.delai_acceptation_minutes', 20)),
        ]);

        // Notifier le prestataire
        $artisan->user->notify(new \App\Notifications\MissionProposeeNotification($mission));

        return redirect()->route('admin.attributions.index')
            ->with('success', 'Prestataire attribué manuellement à la mission #' . $mission->id);
    }
}