<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Artisan;
use App\Models\Metier;
use Illuminate\Support\Str;

class RapprocherMetiersArtisans extends Command
{
    protected $signature = 'artilo:rapprocher-metiers';
    protected $description = 'Relie main_profession (texte libre) au catalogue metiers via metier_id';

    /**
     * Table de correspondance : variantes de texte libre (slug) → slug du métier catalogué
     */
    private array $correspondances = [
        // Plomberie
        'plombier'      => 'plomberie',
        'plomberie'     => 'plomberie',

        // Électricité
        'electricien'   => 'electricite',
        'electricite'   => 'electricite',
        'electricienne' => 'electricite',

        // Maçonnerie
        'macon'         => 'maconnerie',
        'maconnerie'    => 'maconnerie',

        // Peinture
        'peintre'       => 'peinture',
        'peinture'      => 'peinture',

        // Menuiserie
        'menuisier'     => 'menuiserie',
        'menuiserie'    => 'menuiserie',

        // Soudure
        'soudeur'       => 'soudure',
        'soudure'       => 'soudure',

        // Climatisation
        'climaticien'   => 'climatisation',
        'climatisation' => 'climatisation',
        'frigoriste'    => 'climatisation',

        // Jardinage
        'jardinier'     => 'jardinage',
        'jardinage'     => 'jardinage',
        'paysagiste'    => 'jardinage',

        // Nettoyage
        'nettoyeur'     => 'nettoyage',
        'nettoyage'     => 'nettoyage',
        'agent-de-nettoyage' => 'nettoyage',

        // Informatique
        'informaticien' => 'informatique',
        'informatique'  => 'informatique',
        'technicien-informatique' => 'informatique',
    ];

    public function handle(): void
    {
        $metiers = Metier::pluck('id', 'slug');

        Artisan::whereNull('metier_id')->whereNotNull('main_profession')
            ->each(function ($artisan) use ($metiers) {
                $slugBrut = Str::slug($artisan->main_profession);

                // 1. On cherche d'abord une correspondance directe dans le catalogue
                $slugCible = $metiers->has($slugBrut) ? $slugBrut : null;

                // 2. Sinon, on passe par la table de correspondance
                if (!$slugCible && isset($this->correspondances[$slugBrut])) {
                    $slugCible = $this->correspondances[$slugBrut];
                }

                if ($slugCible && isset($metiers[$slugCible])) {
                    $artisan->update(['metier_id' => $metiers[$slugCible]]);
                    $this->info("Artisan #{$artisan->id} ({$artisan->main_profession}) → {$slugCible}");
                } else {
                    $this->warn("Artisan #{$artisan->id} : aucune correspondance pour '{$artisan->main_profession}' (slug: {$slugBrut})");
                }
            });

        $this->info('Rapprochement terminé.');
    }
}