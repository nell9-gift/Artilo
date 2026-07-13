<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Metier;
use Illuminate\Support\Str;

class MetierSeeder extends Seeder
{
    public function run(): void
    {
        $metiers = [
            'Plomberie', 'Électricité', 'Maçonnerie', 'Peinture',
            'Menuiserie', 'Soudure', 'Climatisation', 'Jardinage',
            'Nettoyage', 'Informatique',
        ];

        foreach ($metiers as $nom) {
            Metier::updateOrCreate(
                ['slug' => Str::slug($nom)],
                ['nom' => $nom, 'actif' => true]
            );
        }
    }
}