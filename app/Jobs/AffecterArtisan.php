<?php

namespace App\Jobs;

use App\Models\Mission;
use App\Models\Artisan;
use App\Models\MissionRefus;
use App\Notifications\MissionProposeeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AffecterArtisan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mission;

    public function __construct(Mission $mission)
    {
        $this->mission = $mission;
    }

    public function handle(): void
    {
        Log::info('Début de l\'affectation pour la mission #' . $this->mission->id);

        // 1. Rechercher le meilleur prestataire
        $query = Artisan::query()
            ->where('status', 'approved')  // Uniquement les artisans validés
            ->orderByDesc('average_rating'); // Trier par note

        // Filtrer par métier si la mission a un metier_id
        if ($this->mission->metier_id) {
            $query->where('metier_id', $this->mission->metier_id);
        }

        // Exclure les artisans qui ont déjà refusé cette mission
        $query->whereDoesntHave('missionsRefusees', function ($q) {
            $q->where('mission_id', $this->mission->id);
        });

        // Calculer la distance si des coordonnées sont fournies
        if ($this->mission->latitude && $this->mission->longitude) {
            $query->selectRaw('artisans.*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance_km', [
                $this->mission->latitude,
                $this->mission->longitude,
                $this->mission->latitude
            ])
            ->where(function ($q) {
                $q->whereNull('max_distance_km')
                  ->orWhereRaw('distance_km <= max_distance_km');
            })
            ->orderBy('distance_km');
        }

        $candidat = $query->first();

        if (! $candidat) {
            Log::warning('Aucun prestataire disponible pour la mission #' . $this->mission->id);
            $this->mission->update(['statut' => 'en_attente']);
            return;
        }

        // 2. Affecter la mission au prestataire
        $this->mission->update([
            'artisan_id' => $candidat->id,
            'statut' => 'affectee',
            'affectee_le' => now(),
            'expire_le' => now()->addMinutes(config('artilo.delai_acceptation_minutes', 20)),
        ]);

        Log::info('Mission #' . $this->mission->id . ' affectée au prestataire #' . $candidat->id);

        // 3. Envoyer la notification au prestataire
        $candidat->user->notify(new MissionProposeeNotification($this->mission));
    }
}