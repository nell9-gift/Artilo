<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionRefus;
use App\Jobs\AffecterArtisan;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    /**
     * Liste des missions proposées au prestataire
     */
    public function index()
    {
        $artisan = auth()->user()->artisan;

        $missionsEnAttente = Mission::where('artisan_id', $artisan->id)
            ->where('statut', 'affectee')
            ->where('expire_le', '>', now())
            ->latest()
            ->get();

        $missionsAcceptees = Mission::where('artisan_id', $artisan->id)
            ->whereIn('statut', ['acceptee', 'diagnostic_effectue', 'en_cours'])
            ->latest()
            ->get();

        $missionsTerminees = Mission::where('artisan_id', $artisan->id)
            ->whereIn('statut', ['payee', 'validee_client', 'annulee'])
            ->latest()
            ->get();

        return view('artisan.missions.index', compact(
            'missionsEnAttente',
            'missionsAcceptees',
            'missionsTerminees'
        ));
    }

    /**
     * Accepter une mission
     */
    public function accepter(Mission $mission)
    {
        // Vérifier que le prestataire est bien celui affecté
        abort_unless($mission->artisan_id === auth()->user()->artisan->id, 403);

        // Vérifier que la mission est bien en attente d'acceptation
        abort_unless($mission->statut === 'affectee', 422, 'Cette mission ne peut plus être acceptée.');

        // Vérifier que le délai n'est pas expiré
        abort_unless($mission->expire_le > now(), 422, 'Le délai d\'acceptation est expiré.');

        $mission->update([
            'statut' => 'acceptee',
            'acceptee_le' => now(),
        ]);

        // Notifier le client que le prestataire a accepté
        $mission->particulier->notify(new \App\Notifications\MissionAccepteeNotification($mission));

        return redirect()->route('artisan.missions.index')
            ->with('success', 'Mission acceptée. Vous pouvez maintenant réaliser le diagnostic.');
    }

    /**
     * Refuser une mission
     */
    public function refuser(Request $request, Mission $mission)
    {
        abort_unless($mission->artisan_id === auth()->user()->artisan->id, 403);
        abort_unless($mission->statut === 'affectee', 422, 'Cette mission ne peut plus être refusée.');

        $request->validate([
            'motif' => 'nullable|string|max:500',
        ]);

        // Enregistrer le refus
        MissionRefus::create([
            'mission_id' => $mission->id,
            'artisan_id' => $mission->artisan_id,
            'motif' => $request->motif,
        ]);

        // Remettre la mission en attente
        $mission->update([
            'artisan_id' => null,
            'statut' => 'en_attente',
            'affectee_le' => null,
            'expire_le' => null,
            'refusee_le' => now(),
            'refus_motif' => $request->motif,
        ]);

        // Lancer une nouvelle affectation
        AffecterArtisan::dispatch($mission);

        return redirect()->route('artisan.missions.index')
            ->with('info', 'Mission refusée. Une autre affectation sera proposée.');
    }

    /**
     * Afficher les détails d'une mission
     */
    public function show(Mission $mission)
    {
        abort_unless($mission->artisan_id === auth()->user()->artisan->id, 403);

        return view('artisan.missions.show', compact('mission'));
    }
}