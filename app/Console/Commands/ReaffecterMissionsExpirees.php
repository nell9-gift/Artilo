<?php

namespace App\Console\Commands;

use App\Models\Mission;
use App\Jobs\AffecterArtisan;
use App\Models\MissionRefus;
use Illuminate\Console\Command;

class ReaffecterMissionsExpirees extends Command
{
    protected $signature = 'artilo:reaffecter-missions';
    protected $description = 'Relance l\'affectation pour les missions dont le délai d\'acceptation a expiré';

    public function handle(): void
    {
        $missions = Mission::where('statut', 'affectee')
            ->where('expire_le', '<', now())
            ->get();

        foreach ($missions as $mission) {
            $this->info('Réaffectation de la mission #' . $mission->id);

            // Enregistrer le refus automatique
            MissionRefus::create([
                'mission_id' => $mission->id,
                'artisan_id' => $mission->artisan_id,
                'motif' => 'Non-réponse dans le délai',
            ]);

            // Remettre la mission en attente
            $mission->update([
                'artisan_id' => null,
                'statut' => 'en_attente',
                'affectee_le' => null,
                'expire_le' => null,
            ]);

            // Lancer une nouvelle affectation
            AffecterArtisan::dispatch($mission);
        }

        $this->info($missions->count() . ' mission(s) réaffectée(s).');
    }
}