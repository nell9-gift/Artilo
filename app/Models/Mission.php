<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    // Définition des statuts disponibles (pour les utiliser en code)
    const STATUTS = [
        'en_attente',              // En attente d'affectation
        'affectee',                // Affecté à un prestataire
        'acceptee',                // Accepté par le prestataire
        'diagnostic_effectue',     // Diagnostic réalisé
        'devis_en_attente_validation', // Devis en attente de validation admin
        'devis_valide',            // Devis validé par l'admin
        'devis_envoye',            // Devis envoyé au client
        'devis_refuse',            // Devis refusé par le client
        'acompte_regle',           // Acompte payé par le client
        'en_cours',                // Travaux en cours
        'terminee_prestataire',    // Terminé par le prestataire
        'validee_client',          // Validé par le client
        'solde_regle',             // Solde payé
        'payee',                   // Prestataire payé
        'annulee',                 // Annulée
    ];

    // Statuts terminaux (plus d'actions possibles)
    const STATUTS_TERMINAUX = ['payee', 'annulee'];

    // Statuts où le prestataire est actif sur la mission
    const STATUTS_ACTIFS_PRESTATAIRE = ['acceptee', 'diagnostic_effectue', 'en_cours', 'terminee_prestataire'];

    protected $fillable = [
        'particulier_id',
        'artisan_id',
        'metier_requis',
        'metier_id',              // Nouveau : clé étrangère vers metiers
        'description',
        'statut',
        'latitude',
        'longitude',
        'adresse',
        'photos',                 // Photos du chantier envoyées par le client
        'affectee_le',
        'expire_le',
        'acceptee_le',
        'refusee_le',
        'refus_motif',
        'budget_previsionnel',    // Optionnel
        'date_souhaitee',         // Optionnel
    ];

    protected $casts = [
        'affectee_le' => 'datetime',
        'expire_le' => 'datetime',
        'acceptee_le' => 'datetime',
        'refusee_le' => 'datetime',
        'date_souhaitee' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'photos' => 'array',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Relation: la mission appartient à un particulier (client)
     */
    public function particulier()
    {
        return $this->belongsTo(User::class, 'particulier_id');
    }

    /**
     * Relation: la mission peut être attribuée à un artisan (prestataire)
     */
    public function artisan()
    {
        return $this->belongsTo(Artisan::class);
    }

    /**
     * Relation: la mission est liée à un métier catalogué
     */
    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    /**
     * Relation: la mission a un diagnostic (1-1)
     */
    public function diagnostic()
    {
        return $this->hasOne(Diagnostic::class);
    }

    /**
     * Relation: la mission a un devis (1-1)
     */
    public function devis()
    {
        return $this->hasOne(Devis::class);
    }

    /**
     * Relation: la mission peut avoir plusieurs paiements (acompte, solde)
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Relation: la mission a plusieurs suivis de travaux
     */
    public function suivis()
    {
        return $this->hasMany(SuiviTravaux::class);
    }

    /**
     * Relation: la mission a un reversement (1-1)
     */
    public function reversement()
    {
        return $this->hasOne(Reversement::class);
    }

    /**
     * Relation: la mission peut avoir plusieurs litiges
     */
    public function litiges()
    {
        return $this->hasMany(Litige::class);
    }

    /**
     * Relation: la mission peut avoir plusieurs messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relation: la mission peut avoir plusieurs factures
     */
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Relation: les refus de la mission par les artisans
     */
    public function refus()
    {
        return $this->hasMany(MissionRefus::class);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Récupère le nom du client
     */
    public function getClientNomAttribute(): string
    {
        return $this->particulier?->name ?? 'Client inconnu';
    }

    /**
     * Récupère le nom du prestataire
     */
    public function getPrestataireNomAttribute(): string
    {
        return $this->artisan?->user?->name ?? 'Non attribué';
    }

    /**
     * Récupère le libellé du statut en français
     */
    public function getStatutLibelleAttribute(): string
    {
        $labels = [
            'en_attente' => 'En attente',
            'affectee' => 'Affectée',
            'acceptee' => 'Acceptée',
            'diagnostic_effectue' => 'Diagnostic effectué',
            'devis_en_attente_validation' => 'Devis en validation',
            'devis_valide' => 'Devis validé',
            'devis_envoye' => 'Devis envoyé',
            'devis_refuse' => 'Devis refusé',
            'acompte_regle' => 'Acompte réglé',
            'en_cours' => 'En cours',
            'terminee_prestataire' => 'Terminée par le prestataire',
            'validee_client' => 'Validée par le client',
            'solde_regle' => 'Solde réglé',
            'payee' => 'Payée',
            'annulee' => 'Annulée',
        ];

        return $labels[$this->statut] ?? $this->statut;
    }

    /**
     * Récupère la couleur du statut pour l'affichage
     */
    public function getStatutCouleurAttribute(): string
    {
        $colors = [
            'en_attente' => 'warning',
            'affectee' => 'info',
            'acceptee' => 'primary',
            'diagnostic_effectue' => 'primary',
            'devis_en_attente_validation' => 'warning',
            'devis_valide' => 'success',
            'devis_envoye' => 'info',
            'devis_refuse' => 'danger',
            'acompte_regle' => 'success',
            'en_cours' => 'primary',
            'terminee_prestataire' => 'info',
            'validee_client' => 'success',
            'solde_regle' => 'success',
            'payee' => 'success',
            'annulee' => 'danger',
        ];

        return $colors[$this->statut] ?? 'secondary';
    }

    // ============================================================
    // MÉTHODES MÉTIER
    // ============================================================

    /**
     * Vérifie si la mission est dans un statut terminal
     */
    public function estTerminee(): bool
    {
        return in_array($this->statut, self::STATUTS_TERMINAUX);
    }

    /**
     * Vérifie si la mission est en attente d'une action du prestataire
     */
    public function estEnAttentePrestataire(): bool
    {
        return in_array($this->statut, ['affectee', 'acceptee']);
    }

    /**
     * Vérifie si la mission est en attente d'acceptation par le prestataire
     */
    public function estEnAttenteAcceptation(): bool
    {
        return $this->statut === 'affectee' && $this->expire_le > now();
    }

    /**
     * Vérifie si le délai d'acceptation est expiré
     */
    public function estExpiree(): bool
    {
        return $this->statut === 'affectee' && $this->expire_le < now();
    }

    /**
     * Vérifie si le prestataire peut encore accepter
     */
    public function peutAccepter(): bool
    {
        return $this->statut === 'affectee' && $this->expire_le > now();
    }

    /**
     * Vérifie si le prestataire peut encore refuser
     */
    public function peutRefuser(): bool
    {
        return $this->statut === 'affectee' && $this->expire_le > now();
    }

    /**
     * Vérifie si le prestataire est actif sur cette mission
     */
    public function prestataireActif(): bool
    {
        return in_array($this->statut, self::STATUTS_ACTIFS_PRESTATAIRE);
    }

    /**
     * Vérifie si la mission peut encore être annulée par le particulier
     * (tant qu'elle n'a pas été prise en charge ou finalisée)
     */
    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, ['en_attente', 'affectee']);
    }

    /**
     * Récupère le temps restant avant expiration
     */
    public function getTempsRestantAttribute(): ?string
    {
        if (! $this->expire_le || $this->statut !== 'affectee') {
            return null;
        }

        $minutes = now()->diffInMinutes($this->expire_le, false);

        if ($minutes <= 0) {
            return 'Expiré';
        }

        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $heures = floor($minutes / 60);
        $mins = $minutes % 60;
        return $heures . 'h ' . $mins . 'min';
    }

    /**
     * Calcule le montant de l'acompte (40% du devis)
     */
    public function getMontantAcompteAttribute(): int
    {
        $total = $this->devis?->total_client ?? 0;
        return (int) round($total * config('artilo.taux_acompte', 0.40));
    }

    /**
     * Calcule le solde restant à payer
     */
    public function getSoldeRestantAttribute(): int
    {
        $total = $this->devis?->total_client ?? 0;
        $paye = $this->paiements()->where('statut', 'reussi')->sum('montant');
        return max(0, $total - $paye);
    }

    /**
     * Vérifie si toutes les conditions sont remplies pour le paiement du prestataire
     */
    public function peutPayerPrestataire(): bool
    {
        return $this->statut === 'solde_regle'
            && $this->reversement
            && $this->reversement->statut === 'en_attente';
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope: missions en attente d'affectation
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope: missions affectées (en attente d'acceptation)
     */
    public function scopeAffectees($query)
    {
        return $query->where('statut', 'affectee');
    }

    /**
     * Scope: missions acceptées par le prestataire
     */
    public function scopeAcceptees($query)
    {
        return $query->where('statut', 'acceptee');
    }

    /**
     * Scope: missions en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope: missions terminées
     */
    public function scopeTerminees($query)
    {
        return $query->whereIn('statut', self::STATUTS_TERMINAUX);
    }

    /**
     * Scope: missions par métier
     */
    public function scopeParMetier($query, $metierId)
    {
        return $query->where('metier_id', $metierId);
    }

    /**
     * Scope: missions par artisan
     */
    public function scopeParArtisan($query, $artisanId)
    {
        return $query->where('artisan_id', $artisanId);
    }

    /**
     * Scope: missions par particulier
     */
    public function scopeParParticulier($query, $particulierId)
    {
        return $query->where('particulier_id', $particulierId);
    }

    /**
     * Scope: missions non expirées
     */
    public function scopeNonExpirees($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expire_le')
              ->orWhere('expire_le', '>', now());
        });
    }
}