<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mission;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $artisan = $user->artisan;

        // ============================================================
        // 1. PARAMÈTRES DU SITE
        // ============================================================
        $settings = Setting::pluck('value', 'key');
        $set = fn($key, $default = null) => $settings[$key] ?? $default;

        $siteName = $set('site_name', 'Artilo');
        $logo = $set('logo') ? asset($set('logo')) : null;

        // Couleurs
        $cPrimary = $set('color_primary', '#7C3AED');
        $cPrimaryDark = $set('color_primary_dark', '#6D28D9');
        $cPrimary900 = $set('color_primary_900', '#2E1065');
        $cPrimary800 = $set('color_primary_800', '#4C1D95');
        $cPrimaryLite = $set('color_primary_light', '#A78BFA');
        $cSecondary = $set('color_secondary', '#D97706');
        $cSecondaryLt = $set('color_secondary_light', '#F59E0B');

        // ============================================================
        // 2. DONNÉES RÉELLES DU PRESTATAIRE
        // ============================================================
        $status = $artisan?->status ?? 'pending';
        $statusLabels = [
            'pending' => 'En attente',
            'approved' => 'Validé',
            'rejected' => 'Refusé',
            'incomplete' => 'Profil incomplet',
        ];
        $statusLabel = $statusLabels[$status] ?? ucfirst($status);

        $profession = $artisan?->main_profession ?: $artisan?->profession;
        $niveau = $artisan?->niveau_libelle ?? 'Non défini';
        $score = (int) ($artisan?->score_interne ?? 50);
        $dispo = (bool) ($artisan?->est_disponible ?? false);
        $rating = (float) ($artisan?->average_rating ?? 0);
        $experience = $artisan?->years_experience;
        $momoNumber = $artisan?->mobile_money_number;
        $momoOperator = $artisan?->mobile_money_operator;
        $company = $artisan?->company_name;

        // Zone d'intervention
        $zone = collect([
            $artisan?->intervention_city,
            $artisan?->intervention_prefecture,
            $artisan?->intervention_region,
        ])->filter()->implode(' · ') ?: ($artisan?->intervention_area ?? null);

        // Galerie photos
        $gallery = collect($artisan?->photos ?? [])
            ->map(fn($p) => asset('storage/' . $p))
            ->filter()
            ->take(6)
            ->values();

        // Spécialités, langues, certifications
        $specialties = collect($artisan?->sub_specialties ?? [])->filter()->values();
        $languages = collect($artisan?->languages ?? [])->filter()->values();
        $certifs = collect($artisan?->certifications ?? [])->filter()->values();

        // ============================================================
        // 3. COMPLÉTION DU PROFIL
        // ============================================================
        $profileChecks = [
            'Métier' => filled($profession),
            'Zone' => filled($zone),
            'Adresse' => filled($artisan?->address),
            'Description' => filled($artisan?->description),
            'Photos' => $gallery->isNotEmpty(),
            'Mobile Money' => filled($momoNumber),
            'Document' => filled($artisan?->identity_document) || filled($artisan?->identity_photo_recto),
        ];
        $profileScore = collect($profileChecks)->filter()->count();
        $profilePercent = count($profileChecks) ? (int) round(($profileScore / count($profileChecks)) * 100) : 0;

        // ============================================================
        // 4. MISSIONS RÉELLES
        // ============================================================
        // Missions en attente d'acceptation
        $missionsEnAttente = Mission::where('artisan_id', $artisan->id)
            ->where('statut', 'affectee')
            ->where('expire_le', '>', now())
            ->with(['particulier'])
            ->latest()
            ->get();

        // Missions acceptées
        $missionsAcceptees = Mission::where('artisan_id', $artisan->id)
            ->whereIn('statut', ['acceptee', 'diagnostic_effectue', 'en_cours'])
            ->with(['particulier'])
            ->latest()
            ->get();

        // Missions terminées
        $missionsTerminees = Mission::where('artisan_id', $artisan->id)
            ->whereIn('statut', ['payee', 'validee_client'])
            ->with(['particulier'])
            ->latest()
            ->get();

        // Toutes les missions
        $allMissions = Mission::where('artisan_id', $artisan->id)
            ->with(['particulier'])
            ->latest()
            ->get();

        // ============================================================
        // 5. STATISTIQUES
        // ============================================================
        $nbAttribuees = $allMissions->count();
        $nbActives = $artisan->missions()
            ->whereIn('statut', ['acceptee', 'diagnostic_effectue', 'en_cours'])
            ->count();
        $nbTerminees = $artisan->missions()
            ->whereIn('statut', ['payee', 'validee_client'])
            ->count();

        // Revenus (à remplacer par Reversement plus tard, Phase 20)
        $reversementsTotal = 0; // Temporaire
        $reversementsEnAttente = 0; // Temporaire

        // ============================================================
        // 6. STATS POUR LE DASHBOARD
        // ============================================================
        $stats = [
            ['label' => 'Missions attribuées', 'value' => $nbAttribuees, 'delta' => 'total', 'icon' => 'briefcase', 'tone' => 'primary'],
            ['label' => 'Missions en cours', 'value' => $nbActives, 'delta' => 'actives', 'icon' => 'bolt', 'tone' => 'blue'],
            ['label' => 'Missions terminées', 'value' => $nbTerminees, 'delta' => 'réalisées', 'icon' => 'check', 'tone' => 'green'],
            ['label' => 'Note moyenne', 'value' => $rating > 0 ? number_format($rating, 1) : '—', 'delta' => 'satisfaction', 'icon' => 'star', 'tone' => 'amber'],
        ];

        // ============================================================
        // 7. AUTRES DONNÉES
        // ============================================================
        $repartition = [
            ['label' => 'Plomberie', 'val' => 40],
            ['label' => 'Électricité', 'val' => 28],
            ['label' => 'Maçonnerie', 'val' => 20],
            ['label' => 'Menuiserie', 'val' => 12],
        ];

        $documents = [
            ['name' => "Pièce d'identité", 'type' => 'PDF', 'size' => '1.2 Mo', 'ok' => filled($artisan?->identity_document)],
            ['name' => 'Attestation métier', 'type' => 'PDF', 'size' => '640 Ko', 'ok' => true],
            ['name' => 'Contrat partenaire', 'type' => 'PDF', 'size' => '320 Ko', 'ok' => false],
        ];

        $payments = [
            ['label' => 'Acompte ART-2390', 'method' => $momoOperator ?: 'Moov Money', 'date' => now()->subDays(3)->format('d M'), 'amount' => '+ 158 100 F', 'in' => true],
            ['label' => 'Commission ' . $siteName, 'method' => '7% acompte', 'date' => now()->subDays(3)->format('d M'), 'amount' => '- 11 900 F', 'in' => false],
        ];

        $factoryImage = asset('images/usine.jfif');

        // ============================================================
        // RENVOYER TOUT À LA VUE
        // ============================================================
        return view('artisans.MaPage', compact(
            'artisan',
            'user',
            'siteName',
            'logo',
            'cPrimary',
            'cPrimaryDark',
            'cPrimary900',
            'cPrimary800',
            'cPrimaryLite',
            'cSecondary',
            'cSecondaryLt',
            'status',
            'statusLabel',
            'profession',
            'niveau',
            'score',
            'dispo',
            'rating',
            'experience',
            'momoNumber',
            'momoOperator',
            'company',
            'zone',
            'gallery',
            'specialties',
            'languages',
            'certifs',
            'profileChecks',
            'profileScore',
            'profilePercent',
            'missionsEnAttente',
            'missionsAcceptees',
            'missionsTerminees',
            'allMissions',
            'nbAttribuees',
            'nbActives',
            'nbTerminees',
            'reversementsTotal',
            'reversementsEnAttente',
            'stats',
            'repartition',
            'documents',
            'payments',
            'factoryImage'
        ));
    }
}