<?php

return [
     /*
    |--------------------------------------------------------------------------
    | COMMISSION ARTILO
    |--------------------------------------------------------------------------
    | Commission retenue par Artilo sur chaque reversement au prestataire
    */
    'commission_prestataire' => 0.10, // 10%

    /*
    |--------------------------------------------------------------------------
    | DÉLAI D'ACCEPTATION
    |--------------------------------------------------------------------------
    | Temps (en minutes) laissé à un prestataire pour accepter une mission
    */
    'delai_acceptation_minutes' => 20,

    /*
    |--------------------------------------------------------------------------
    | TAUX DE L'ACOMPTE
    |--------------------------------------------------------------------------
    | Pourcentage du devis demandé au client avant le début des travaux
    */
    'taux_acompte' => 0.40, // 40%

    /*
    |--------------------------------------------------------------------------
    | DEVISE
    |--------------------------------------------------------------------------
    | Monnaie utilisée sur la plateforme
    */
    'devise' => 'XOF',

    /*
    |--------------------------------------------------------------------------
    | LISTE DES MÉTIERS (provisoire, sera remplacée par le catalogue en Phase 8)
    |--------------------------------------------------------------------------
    */
    'metiers' => [
        'Electricien',
        'Plombier',
        'Maçon',
        'Peintre',
        'Menuisier',
        'Soudeur',
        'Climatisation',
        'Jardinage',
        'Nettoyage',
        'Informatique',
        'Coffreur',
        'Carreleur',
        'Charpentier',
        'Mécanicien',
    ],
    
    'geolocation' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY', ''),
        'nominatim_url' => 'https://nominatim.openstreetmap.org/reverse',
    ],
    
    'mission' => [
        'expiration_minutes' => 20,
        'max_photos' => 5,
        'max_photo_size' => 2048, // en KB
    ],
     /*
    |--------------------------------------------------------------------------
    | MARGE SUR DEVIS (Phase 12)
    |--------------------------------------------------------------------------
    | Marge appliquée par Artilo sur le coût total (prestations + main d'œuvre)
    */
    'marge_devis' => 0.20, // 20%
];