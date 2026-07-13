<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artisan extends Model
{
    // Fields allowed to be filled when creating or updating
    protected $fillable = [
        'user_id',
        'profession',
        'intervention_area',
        'identity_document',
        'status',
        'refusal_reason',
        'schedules',
        'description',
        'years_experience',
        'photos',
        'average_rating',
        'address',
        'latitude',
        'longitude',
        'mobile_money_number',
        'mobile_money_operator',
        'availability_start_time',
        'availability_end_time',
        'max_distance_km',
        'identity_document_type',
        'identity_document_number',
        'identity_photo_recto',
        'identity_photo_verso',
        'identity_selfie',
        'identity_expiration_date',
        'main_profession',
        'sub_specialties',
        'diplomas',
        'certifications',
        'company_name',
        'company_registration_number',
        'intervention_region',
        'intervention_prefecture',
        'intervention_city',
        'languages',
        // NOUVEAUX CHAMPS Phase 6
        'metier_id',           // Clé étrangère vers le catalogue des métiers
        'est_disponible',      // Disponibilité du prestataire
        'score_interne',       // Score interne (0-100)
        'niveau',              // debutant, confirme, expert
    ];

    // Automatic conversion of fields to PHP types
    protected $casts = [
        'schedules' => 'array',
        'photos' => 'array',
        'sub_specialties' => 'array',
        'diplomas' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'availability_start_time' => 'datetime:H:i',
        'availability_end_time' => 'datetime:H:i',
        'identity_expiration_date' => 'date',
        'max_distance_km' => 'integer',
        'est_disponible' => 'boolean',
        'score_interne' => 'integer',
    ];

    // Définition des niveaux possibles
    const NIVEAUX = [
        'debutant' => 'Débutant',
        'confirme' => 'Confirmé',
        'expert' => 'Expert',
    ];

    // Définition des statuts possibles
    const STATUTS = [
        'pending' => 'En attente',
        'approved' => 'Validé',
        'rejected' => 'Refusé',
        'incomplete' => 'Profil incomplet',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Relation: un artisan appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: un artisan appartient à un métier catalogué (NOUVEAU Phase 6)
     */
    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    /**
     * Relation: un artisan peut avoir plusieurs missions
     */
    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    /**
     * Relation: un artisan peut refuser plusieurs missions
     */
    public function missionsRefusees()
    {
        return $this->hasMany(MissionRefus::class);
    }

    /**
     * Relation: un artisan peut avoir plusieurs reversements
     */
    public function reversements()
    {
        return $this->hasMany(Reversement::class);
    }

    /**
     * Relation: un artisan peut faire plusieurs diagnostics
     */
    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    /**
     * Relation: un artisan peut avoir plusieurs suivis de travaux
     */
    public function suivisTravaux()
    {
        return $this->hasManyThrough(SuiviTravaux::class, Mission::class);
    }

    // ============================================================
    // ACCESSORS & MUTATORS
    // ============================================================

    /**
     * Récupère le nom complet de l'artisan via son utilisateur
     */
    public function getNomAttribute(): string
    {
        return $this->user?->name ?? 'Prestataire';
    }

    /**
     * Récupère l'email de l'artisan via son utilisateur
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    /**
     * Récupère le libellé du niveau
     */
    public function getNiveauLibelleAttribute(): string
    {
        return self::NIVEAUX[$this->niveau] ?? $this->niveau ?? 'Non défini';
    }

    /**
     * Récupère le libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return self::STATUTS[$this->status] ?? $this->status ?? 'Inconnu';
    }

    // ============================================================
    // MÉTHODES MÉTIER
    // ============================================================

    /**
     * Récupère le salaire journalier du prestataire selon son métier et son niveau
     * (Phase 9 - à utiliser quand SalaireJournalier sera créé)
     */
    public function salaireJournalier(): ?SalaireJournalier
    {
        if (! $this->metier_id) {
            return null;
        }

        return SalaireJournalier::where('metier_id', $this->metier_id)
            ->where('niveau', $this->niveau ?? 'confirme')
            ->where('actif', true)
            ->first();
    }

    /**
     * Vérifie si l'artisan est disponible pour une mission
     */
    public function estDisponiblePourMission(Mission $mission): bool
    {
        // Vérifier la disponibilité générale
        if (! $this->est_disponible) {
            return false;
        }

        // Vérifier si l'artisan a déjà refusé cette mission
        if ($this->missionsRefusees()->where('mission_id', $mission->id)->exists()) {
            return false;
        }

        // Vérifier le métier
        if ($this->metier_id && $mission->metier_id && $this->metier_id !== $mission->metier_id) {
            return false;
        }

        // Vérifier la zone d'intervention (si coordonnées disponibles)
        if ($mission->latitude && $mission->longitude && $this->max_distance_km) {
            $distance = $this->calculerDistance(
                $this->latitude,
                $this->longitude,
                $mission->latitude,
                $mission->longitude
            );

            if ($distance > $this->max_distance_km) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcule la distance en kilomètres entre deux points GPS
     * (Formule de Haversine)
     */
    public function calculerDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (! $lat1 || ! $lon1 || ! $lat2 || ! $lon2) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371; // Rayon de la Terre en km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    /**
     * Met à jour le score interne de l'artisan
     * (À appeler après chaque mission terminée)
     */
    public function mettreAJourScore(): void
    {
        $missionsAcceptees = $this->missions()->whereIn('statut', ['payee', 'validee_client'])->count();
        $missionsTotales = $this->missions()->count();

        if ($missionsTotales === 0) {
            $this->score_interne = 50;
            $this->save();
            return;
        }

        // Taux d'acceptation
        $tauxAcceptation = ($missionsAcceptees / $missionsTotales) * 100;

        // Note moyenne (si disponible)
        $noteMoyenne = $this->average_rating ?? 0;
        $noteScore = ($noteMoyenne / 5) * 100;

        // Score final (pondéré)
        $this->score_interne = (int) round(($tauxAcceptation * 0.6) + ($noteScore * 0.4));
        $this->save();
    }

    /**
     * Vérifie si le profil est complet
     */
    public function estComplet(): bool
    {
        $required = [
            'main_profession',
            'intervention_area',
            'description',
            'address',
            'latitude',
            'longitude',
            'mobile_money_number',
            'mobile_money_operator',
        ];

        foreach ($required as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Récupère le nombre de missions en cours
     */
    public function getMissionsEnCoursAttribute(): int
    {
        return $this->missions()
            ->whereIn('statut', ['acceptee', 'diagnostic_effectue', 'en_cours'])
            ->count();
    }

    /**
     * Récupère le nombre de missions terminées
     */
    public function getMissionsTermineesAttribute(): int
    {
        return $this->missions()
            ->whereIn('statut', ['payee', 'validee_client'])
            ->count();
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope: filtrer les artisans disponibles
     */
    public function scopeDisponible($query)
    {
        return $query->where('est_disponible', true);
    }

    /**
     * Scope: filtrer les artisans par métier
     */
    public function scopeParMetier($query, $metierId)
    {
        return $query->where('metier_id', $metierId);
    }

    /**
     * Scope: filtrer les artisans par statut
     */
    public function scopeParStatut($query, $statut)
    {
        return $query->where('status', $statut);
    }

    /**
     * Scope: filtrer les artisans par niveau
     */
    public function scopeParNiveau($query, $niveau)
    {
        return $query->where('niveau', $niveau);
    }

    /**
     * Scope: filtrer les artisans par rayon de distance
     */
    public function scopeDansRayon($query, $latitude, $longitude, $rayonKm)
    {
        return $query->whereRaw("
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) <= ?
        ", [$latitude, $longitude, $latitude, $rayonKm]);
    }

    /**
     * Scope: trier par score interne (du plus élevé au plus bas)
     */
    public function scopeMeilleurScore($query)
    {
        return $query->orderByDesc('score_interne');
    }
}