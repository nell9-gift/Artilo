<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Setting;

$user = Auth::user();

/* ─────────────────────────────────────────────────────────────
 |  RÉGLAGES GLOBAUX (SettingSeeder) — branding, couleurs, images
 ───────────────────────────────────────────────────────────── */
$settings = Setting::pluck('value', 'key');
$set = fn ($key, $default = null) => $settings[$key] ?? $default;

/* Résout n'importe quel chemin d'image (public/images, storage, url absolue) */
$img = function ($path, $fallback = null) {
    if (blank($path)) {
        return $fallback;
    }
    if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
        return $path;
    }
    if (Str::startsWith($path, 'images/')) {
        return asset($path);
    }
    return Storage::disk('public')->exists($path) ? asset('storage/'.$path) : asset($path);
};

$siteName     = $set('site_name', 'Artilo');
$logo         = $img($set('logo'));
$contactPhone = $set('contact_phone', '');
$contactCity  = $set('contact_city', 'Lomé, Togo');

/* Palette pilotée depuis les réglages (reste synchro avec l'admin) */
$cPrimary     = $set('color_primary', '#7C3AED');
$cPrimaryDark = $set('color_primary_dark', '#6D28D9');
$cPrimary900  = $set('color_primary_900', '#2E1065');
$cPrimary800  = $set('color_primary_800', '#4C1D95');
$cPrimaryLite = $set('color_primary_light', '#A78BFA');
$cSecondary   = $set('color_secondary', '#D97706');
$cSecondaryLt = $set('color_secondary_light', '#F59E0B');

/* ─────────────────────────────────────────────────────────────
 |  DONNÉES RÉELLES DU PRESTATAIRE (modèle Artisan)
 ───────────────────────────────────────────────────────────── */
$status = $artisan?->status ?? 'pending';
$statusLabels = [
    'pending'    => 'En attente',
    'approved'   => 'Validé',
    'rejected'   => 'Refusé',
    'incomplete' => 'Profil incomplet',
];
$statusLabel = $statusLabels[$status] ?? ucfirst($status);

$profession   = $artisan?->main_profession ?: $artisan?->profession;
$niveau       = $artisan?->niveau_libelle ?? 'Non défini';
$score        = (int) ($artisan?->score_interne ?? 50);
$dispo        = (bool) ($artisan?->est_disponible ?? false);
$rating       = (float) ($artisan?->average_rating ?? 0);
$experience   = $artisan?->years_experience;
$gallery      = collect($artisan?->photos ?? [])->map(fn ($p) => $img($p))->filter()->take(6)->values();
$specialties  = collect($artisan?->sub_specialties ?? [])->filter()->values();
$languages    = collect($artisan?->languages ?? [])->filter()->values();
$certifs      = collect($artisan?->certifications ?? [])->filter()->values();
$momoNumber   = $artisan?->mobile_money_number;
$momoOperator = $artisan?->mobile_money_operator;
$company      = $artisan?->company_name;

$zone = collect([
    $artisan?->intervention_city,
    $artisan?->intervention_prefecture,
    $artisan?->intervention_region,
])->filter()->implode(' · ') ?: ($artisan?->intervention_area ?? null);

/* Complétion du profil (méthode métier estComplet) */
$profileChecks = [
    'Métier'      => filled($profession),
    'Zone'        => filled($zone),
    'Adresse'     => filled($artisan?->address),
    'Description' => filled($artisan?->description),
    'Photos'      => $gallery->isNotEmpty(),
    'Mobile Money'=> filled($momoNumber),
    'Document'    => filled($artisan?->identity_document) || filled($artisan?->identity_photo_recto),
];
$profileScore   = collect($profileChecks)->filter()->count();
$profilePercent = count($profileChecks) ? (int) round(($profileScore / count($profileChecks)) * 100) : 0;

/* Compteurs missions — vraies valeurs via les accessors du modèle, avec repli démo */
$nbAttribuees = $artisan ? $artisan->missions()->count() : 12;
$nbActives    = $artisan?->missions_en_cours ?? 4;
$nbTerminees  = $artisan?->missions_terminees ?? 8;

$stats = [
    ['label' => 'Missions attribuées', 'value' => $nbAttribuees,        'delta' => 'total',       'icon' => 'briefcase', 'tone' => 'primary'],
    ['label' => 'Missions en cours',   'value' => $nbActives,           'delta' => 'actives',     'icon' => 'bolt',      'tone' => 'blue'],
    ['label' => 'Missions terminées',  'value' => $nbTerminees,         'delta' => 'réalisées',   'icon' => 'check',     'tone' => 'green'],
    ['label' => 'Note moyenne',        'value' => $rating > 0 ? number_format($rating, 1) : '—', 'delta' => 'satisfaction', 'icon' => 'star', 'tone' => 'amber'],
];

/* ─────────────────────────────────────────────────────────────
 |  MISSIONS RÉELLES (depuis le contrôleur DashboardController)
 ───────────────────────────────────────────────────────────── */
$allMissionsList = collect();
$missionsEnAttente->each(fn($m) => $m->state = 'new');
$missionsAcceptees->each(fn($m) => $m->state = 'progress');
$allMissionsList = $missionsEnAttente->concat($missionsAcceptees);

function formatMissionDate($date) {
    if (!$date) return '—';
    $n = \Carbon\Carbon::parse($date);
    if ($n->isToday()) return "Aujourd'hui · " . $n->format('H:i');
    if ($n->isTomorrow()) return 'Demain · ' . $n->format('H:i');
    return $n->format('d M Y · H:i');
}

$missions = $allMissionsList->map(fn($m) => [
    'id' => '#' . $m->id,
    'client' => $m->particulier->name ?? 'Client',
    'type' => $m->metier_requis ?? ($m->metier->nom ?? 'Service'),
    'zone' => $m->adresse ?? 'Adresse non renseignée',
    'date' => formatMissionDate($m->created_at),
    'budget' => $m->budget_previsionnel ? number_format($m->budget_previsionnel, 0, ',', ' ') . ' F' : '—',
    'state' => $m->state,
    'urgent' => ($m->expire_le && now()->diffInMinutes($m->expire_le, false) < 10 && $m->statut === 'affectee'),
    'statut' => $m->statut,
    'mission' => $m,
])->values()->toArray();
$history = [
    ['id' => 'ART-2201', 'type' => 'Menuiserie',  'client' => 'Sena T.',  'date' => '02 Juil 2026', 'amount' => '210 000 F', 'note' => 5],
    ['id' => 'ART-2188', 'type' => 'Plomberie',   'client' => 'Koffi M.', 'date' => '28 Juin 2026', 'amount' => '85 000 F',  'note' => 4],
    ['id' => 'ART-2170', 'type' => 'Électricité', 'client' => 'Afi L.',   'date' => '19 Juin 2026', 'amount' => '150 000 F', 'note' => 5],
];
$payments = [
    ['label' => 'Acompte ART-2390',   'method' => $momoOperator ?: 'Moov Money', 'date' => '10 Juil', 'amount' => '+ 158 100 F', 'in' => true],
    ['label' => 'Solde ART-2188',     'method' => 'Yas',                          'date' => '30 Juin', 'amount' => '+ 79 050 F',  'in' => true],
    ['label' => 'Commission '.$siteName, 'method' => '7% acompte',                'date' => '10 Juil', 'amount' => '- 11 900 F',  'in' => false],
];
$documents = [
    ['name' => "Pièce d'identité",   'type' => 'PDF', 'size' => '1.2 Mo', 'ok' => filled($artisan?->identity_document)],
    ['name' => 'Attestation métier', 'type' => 'PDF', 'size' => '640 Ko', 'ok' => true],
    ['name' => 'Contrat partenaire', 'type' => 'PDF', 'size' => '320 Ko', 'ok' => false],
];
$repartition = [
    ['label' => 'Plomberie',   'val' => 40],
    ['label' => 'Électricité', 'val' => 28],
    ['label' => 'Maçonnerie',  'val' => 20],
    ['label' => 'Menuiserie',  'val' => 12],
];

// Image "usine.jfif" - placez votre image dans public/images/usine.jfif
$factoryImage = asset('images/usine.jfif');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Prestataire — {{ $siteName }}</title>
    @if ($favicon = $img($set('favicon')))
        <link rel="icon" href="{{ $favicon }}">
    @endif
    <style>
        *{margin:0;padding:0;box-sizing:border-box}

        .artilo-pro{
            --p-primary:{{ $cPrimary }};
            --p-primary-dark:{{ $cPrimaryDark }};
            --p-primary-900:{{ $cPrimary900 }};
            --p-primary-800:{{ $cPrimary800 }};
            --p-primary-light:{{ $cPrimaryLite }};
            --p-secondary:{{ $cSecondary }};
            --p-secondary-light:{{ $cSecondaryLt }};
            --p-blue:#2563EB;
            --p-green:#059669;
            --p-red:#DC2626;

            --p-ink:#1B1230;
            --p-text:#241542;
            --p-muted:#6B6480;
            --p-faint:#8A83A0;

            --p-bg:#F4F2FA;
            --p-surface:#FFFFFF;
            --p-surface-2:#FAF9FE;
            --p-border:#EAE6F5;
            --p-border-strong:#DED8EF;

            --p-radius:18px;
            --p-radius-sm:12px;
            --p-shadow:0 1px 2px rgba(27,18,48,.04), 0 12px 32px -18px rgba(27,18,48,.22);
            --p-shadow-lg:0 24px 60px -30px rgba(27,18,48,.35);

            --side-w:270px;

            font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;
            color:var(--p-text);
            background:var(--p-bg);
            min-height:100vh;
            -webkit-font-smoothing:antialiased;
            text-rendering:optimizeLegibility;
        }
        .artilo-pro *{font-family:inherit}

        .pro-shell{display:flex;min-height:100vh}

        /* ===== SIDEBAR ===== */
        .pro-side{
            width:var(--side-w);
            flex-shrink:0;
            background:
                radial-gradient(120% 80% at 0% 0%, rgba(167,139,250,.16), transparent 55%),
                linear-gradient(180deg,var(--p-primary-900),var(--p-ink));
            color:#EDE9F7;
            display:flex;
            flex-direction:column;
            padding:1.4rem 1.1rem;
            position:sticky;
            top:0;
            height:100vh;
            z-index:60;
        }
        .pro-brand{display:flex;align-items:center;gap:.7rem;padding:.2rem .3rem 1.3rem}
        .pro-brand .logo{
            width:42px;height:42px;border-radius:12px;flex-shrink:0;
            display:grid;place-items:center;overflow:hidden;
            background:linear-gradient(135deg,var(--p-primary),var(--p-secondary));
            color:#fff;font-weight:800;font-size:1.15rem;
            box-shadow:0 8px 20px -8px rgba(124,58,237,.7);
        }
        .pro-brand .logo img{width:100%;height:100%;object-fit:cover}
        .pro-brand .brand-text b{display:block;font-size:1.05rem;letter-spacing:-.01em;color:#fff}
        .pro-brand .brand-text small{font-size:.72rem;color:#B8AFD6;letter-spacing:.04em;text-transform:uppercase}

        .pro-profile{
            display:flex;align-items:center;gap:.75rem;
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.08);
            border-radius:14px;padding:.7rem;margin-bottom:1.3rem;
        }
        .pro-avatar{
            width:44px;height:44px;border-radius:11px;flex-shrink:0;
            display:grid;place-items:center;font-weight:800;font-size:1.1rem;color:#fff;
            background:linear-gradient(135deg,var(--p-primary-light),var(--p-primary));
        }
        .pro-profile .who{min-width:0}
        .pro-profile .who b{display:block;font-size:.9rem;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

        .pro-badge{
            display:inline-flex;align-items:center;gap:.35rem;
            font-size:.7rem;font-weight:700;margin-top:.15rem;
            padding:.15rem .5rem;border-radius:999px;
            background:rgba(255,255,255,.1);color:#D8D0EC;
        }
        .pro-badge .dot{width:6px;height:6px;border-radius:50%;background:currentColor}
        .pro-badge.approved{background:rgba(5,150,105,.22);color:#6EE7B7}
        .pro-badge.pending{background:rgba(217,119,6,.22);color:#FCD34D}
        .pro-badge.rejected,.pro-badge.incomplete{background:rgba(220,38,38,.22);color:#FCA5A5}

        .pro-nav{display:flex;flex-direction:column;gap:.15rem;overflow-y:auto;flex:1;margin:0 -.3rem;padding:0 .3rem}
        .pro-nav .cat{font-size:.66rem;text-transform:uppercase;letter-spacing:.11em;color:#8479A8;margin:1rem .6rem .4rem}
        .pro-nav .cat:first-child{margin-top:0}
        .pro-link{
            display:flex;align-items:center;gap:.7rem;
            width:100%;border:0;cursor:pointer;text-align:left;
            padding:.62rem .7rem;border-radius:11px;
            background:transparent;color:#C9C1E0;font-size:.86rem;font-weight:600;
            transition:.18s;
        }
        .pro-link svg{width:18px;height:18px;flex-shrink:0;opacity:.85}
        .pro-link:hover{background:rgba(255,255,255,.07);color:#fff}
        .pro-link.active{
            background:linear-gradient(135deg,var(--p-primary),var(--p-primary-dark));
            color:#fff;box-shadow:0 10px 22px -12px rgba(124,58,237,.9);
        }
        .pro-link.active svg{opacity:1}
        .pro-link .count{
            margin-left:auto;background:var(--p-secondary);color:#fff;
            font-size:.68rem;font-weight:800;padding:.05rem .45rem;border-radius:999px;
        }

        .pro-logout{margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.09)}
        .pro-logout button{
            display:flex;align-items:center;gap:.6rem;width:100%;
            background:transparent;border:1px solid rgba(255,255,255,.14);
            color:#D8D0EC;padding:.62rem .7rem;border-radius:11px;
            font-size:.84rem;font-weight:600;cursor:pointer;transition:.18s;
        }
        .pro-logout button:hover{background:rgba(220,38,38,.16);border-color:rgba(220,38,38,.4);color:#FCA5A5}

        /* ===== MAIN ===== */
        .pro-main{flex:1;min-width:0;display:flex;flex-direction:column}

        .pro-topbar{
            position:sticky;top:0;z-index:40;
            display:flex;align-items:center;gap:.8rem;
            padding:.85rem 1.6rem;
            background:rgba(244,242,250,.82);
            backdrop-filter:blur(14px);
            border-bottom:1px solid var(--p-border);
        }
        .pro-search{
            flex:1;max-width:440px;display:flex;align-items:center;gap:.55rem;
            background:var(--p-surface);border:1px solid var(--p-border);
            border-radius:12px;padding:.55rem .8rem;transition:.18s;
        }
        .pro-search:focus-within{border-color:var(--p-primary-light);box-shadow:0 0 0 3px rgba(124,58,237,.12)}
        .pro-search svg{width:17px;height:17px;color:var(--p-faint);flex-shrink:0}
        .pro-search input{border:0;outline:0;background:transparent;width:100%;font-size:.88rem;color:var(--p-text)}

        .top-spacer{flex:1}
        .avail-pill{
            display:inline-flex;align-items:center;gap:.5rem;
            padding:.45rem .8rem;border-radius:999px;font-size:.78rem;font-weight:700;
            border:1px solid var(--p-border);background:var(--p-surface);
        }
        .avail-pill .dt{width:8px;height:8px;border-radius:50%}
        .avail-pill.on{color:var(--p-green);border-color:rgba(5,150,105,.3);background:rgba(5,150,105,.08)}
        .avail-pill.on .dt{background:var(--p-green);box-shadow:0 0 0 3px rgba(5,150,105,.18)}
        .avail-pill.off{color:var(--p-muted)}
        .avail-pill.off .dt{background:var(--p-faint)}

        .pro-icon-btn{
            position:relative;width:40px;height:40px;flex-shrink:0;
            display:grid;place-items:center;border-radius:11px;cursor:pointer;
            background:var(--p-surface);border:1px solid var(--p-border);color:var(--p-text);
            transition:.18s;text-decoration:none;
        }
        .pro-icon-btn:hover{border-color:var(--p-primary-light);color:var(--p-primary)}
        .pro-icon-btn svg{width:18px;height:18px}
        .pro-icon-btn .ping{
            position:absolute;top:-5px;right:-5px;min-width:17px;height:17px;padding:0 4px;
            background:var(--p-red);color:#fff;font-size:.62rem;font-weight:800;
            border-radius:999px;display:grid;place-items:center;border:2px solid var(--p-bg);
        }
        .pro-side-toggle{display:none}

        .pro-panel{display:none;padding:1.6rem;max-width:1180px;width:100%;margin:0 auto;animation:fade .3s ease}
        .pro-panel.active{display:block}
        @keyframes fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

        /* ===== HERO AVEC IMAGE DE FOND ===== */
        .pro-hero{
            position:relative;
            overflow:hidden;
            color:#fff;
            border-radius:var(--p-radius);
            padding:2.2rem 2.2rem;
            min-height:260px;
            display:flex;
            align-items:center;
            box-shadow:var(--p-shadow-lg);
            background: var(--p-primary-900);
        }

        /* Image de fond */
        .pro-hero .hero-bg{
            position:absolute;
            inset:0;
            z-index:0;
            background-image: url('{{ $factoryImage }}');
            background-size: cover;
            background-position: center right;
            opacity:0.5;
        }

        /* Dégradé léger par-dessus l'image pour lisibilité */
        .pro-hero .hero-overlay{
            position:absolute;
            inset:0;
            z-index:1;
            background: linear-gradient(135deg, rgba(46,16,101,0.75) 0%, rgba(46,16,101,0.3) 60%, rgba(46,16,101,0.1) 100%);
        }

        .pro-hero > *:not(.hero-bg):not(.hero-overlay){
            position:relative;
            z-index:2;
        }

        .pro-hero .eyebrow{
            color:rgba(255,255,255,.85);
            font-size:.75rem;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.12em;
        }
        .pro-hero h1{
            font-size:clamp(1.6rem, 3.2vw, 2.4rem);
            font-weight:800;
            letter-spacing:-.02em;
            margin:.35rem 0 .5rem;
            text-wrap:balance;
        }
        .pro-hero p{
            color:rgba(255,255,255,.9);
            font-size:.95rem;
            line-height:1.6;
            max-width:52ch;
        }
        .cta{
            display:flex;
            flex-wrap:wrap;
            gap:.7rem;
            margin-top:1.2rem;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            cursor:pointer;
            padding:.68rem 1.1rem;
            border-radius:12px;
            font-size:.86rem;
            font-weight:700;
            border:1px solid transparent;
            text-decoration:none;
            transition:.18s;
        }
        .btn svg{width:16px;height:16px}
        .btn.solid{background:#fff;color:var(--p-primary-dark)}
        .btn.solid:hover{transform:translateY(-1px);box-shadow:0 12px 24px -12px rgba(0,0,0,.5)}
        .btn.ghost{background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.28)}
        .btn.ghost:hover{background:rgba(255,255,255,.24)}

        .eyebrow{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:var(--p-primary)}

        /* ===== CARDS / LAYOUT ===== */
        .card{
            background:var(--p-surface);border:1px solid var(--p-border);
            border-radius:var(--p-radius);padding:1.3rem;box-shadow:var(--p-shadow);
        }
        .grid-2{display:grid;grid-template-columns:1fr;gap:1rem}
        .grid-2b{display:grid;grid-template-columns:1fr;gap:1rem}
        @media(min-width:920px){
            .grid-2{grid-template-columns:1.4fr 1fr}
            .grid-2b{grid-template-columns:1fr 1fr}
        }
        .sec-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1rem}
        .sec-head h3{font-size:1.05rem;font-weight:800;letter-spacing:-.01em;margin-top:.15rem}
        .chip{
            display:inline-flex;align-items:center;gap:.35rem;
            font-size:.74rem;font-weight:700;color:var(--p-primary);
            background:rgba(124,58,237,.09);padding:.3rem .65rem;border-radius:999px;
            border:1px solid rgba(124,58,237,.14);white-space:nowrap;
        }

        /* ===== STATS ===== */
        .pro-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem}
        @media(min-width:820px){.pro-stats{grid-template-columns:repeat(4,1fr)}}
        .stat{
            position:relative;background:var(--p-surface);border:1px solid var(--p-border);
            border-radius:var(--p-radius);padding:1.15rem;box-shadow:var(--p-shadow);overflow:hidden;
        }
        .stat::after{content:"";position:absolute;inset:0 0 auto auto;width:70px;height:70px;
            border-radius:0 0 0 100%;opacity:.1;background:currentColor}
        .stat .ico{width:40px;height:40px;border-radius:11px;display:grid;place-items:center;margin-bottom:.7rem;color:#fff}
        .stat .ico svg{width:20px;height:20px}
        .stat .num{font-size:1.55rem;font-weight:900;letter-spacing:-.02em;line-height:1}
        .stat .lab{font-size:.78rem;color:var(--p-muted);margin-top:.25rem;font-weight:600}
        .stat .delta{font-size:.72rem;font-weight:700;margin-top:.5rem;color:var(--p-faint)}
        .stat.primary{color:var(--p-primary)} .stat.primary .ico{background:var(--p-primary)}
        .stat.blue{color:var(--p-blue)}       .stat.blue .ico{background:var(--p-blue)}
        .stat.green{color:var(--p-green)}     .stat.green .ico{background:var(--p-green)}
        .stat.amber{color:var(--p-secondary)} .stat.amber .ico{background:var(--p-secondary)}

        /* ===== MISSIONS ===== */
        .mission{
            position:relative;border:1px solid var(--p-border);border-radius:14px;
            padding:1rem;margin-bottom:.8rem;transition:.18s;background:var(--p-surface-2);
        }
        .mission:last-child{margin-bottom:0}
        .mission:hover{border-color:var(--p-border-strong);box-shadow:var(--p-shadow)}
        .mission .top{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start}
        .mission .id{font-size:.7rem;font-weight:800;color:var(--p-primary);letter-spacing:.04em}
        .mission h4{font-size:.95rem;font-weight:700;margin:.2rem 0 .5rem}
        .mission .meta{display:flex;flex-wrap:wrap;gap:.8rem;font-size:.76rem;color:var(--p-muted)}
        .mission .meta span{display:inline-flex;align-items:center;gap:.3rem}
        .mission .meta svg{width:13px;height:13px}
        .budget{font-size:1rem;font-weight:900;color:var(--p-text);white-space:nowrap}
        .urgent-flag{
            position:absolute;top:-8px;left:14px;font-size:.62rem;font-weight:800;letter-spacing:.05em;
            background:var(--p-red);color:#fff;padding:.15rem .5rem;border-radius:999px;
        }
        .state-tag{font-size:.7rem;font-weight:800;padding:.22rem .6rem;border-radius:999px}
        .state-tag.new{background:rgba(217,119,6,.14);color:var(--p-secondary)}
        .state-tag.accepted{background:rgba(37,99,235,.13);color:var(--p-blue)}
        .state-tag.progress{background:rgba(124,58,237,.12);color:var(--p-primary)}
        .acts{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.9rem;padding-top:.9rem;border-top:1px dashed var(--p-border-strong)}
        .m-btn{
            display:inline-flex;align-items:center;gap:.4rem;cursor:pointer;
            font-size:.78rem;font-weight:700;padding:.5rem .8rem;border-radius:10px;
            border:1px solid var(--p-border-strong);background:var(--p-surface);color:var(--p-text);transition:.18s;
        }
        .m-btn svg{width:14px;height:14px}
        .m-btn:hover{border-color:var(--p-primary-light)}
        .m-btn.accept{background:var(--p-green);border-color:var(--p-green);color:#fff}
        .m-btn.refuse{background:transparent;border-color:rgba(220,38,38,.35);color:var(--p-red)}
        .m-btn.diag{color:var(--p-blue)}
        .m-btn.devis{background:var(--p-primary);border-color:var(--p-primary);color:#fff}

        /* ===== ROWS / LISTS ===== */
        .row{
            display:flex;align-items:center;justify-content:space-between;gap:1rem;
            padding:.75rem .9rem;border:1px solid var(--p-border);border-radius:12px;
            margin-bottom:.6rem;background:var(--p-surface-2);
        }
        .row:last-child{margin-bottom:0}
        .row .l b{display:block;font-size:.88rem;font-weight:700}
        .row .l small{font-size:.74rem;color:var(--p-muted)}
        .amt{font-weight:900;font-size:.92rem}
        .amt.in{color:var(--p-green)} .amt.out{color:var(--p-red)}
        .stars{color:var(--p-secondary-light);letter-spacing:.05em;font-size:.9rem}
        .empty{font-size:.82rem;color:var(--p-muted);line-height:1.6}

        /* ===== FORM FIELDS ===== */
        .fld{width:100%;padding:.65rem .8rem;border:1px solid var(--p-border-strong);
            border-radius:11px;font-size:.88rem;color:var(--p-text);background:var(--p-surface);outline:0;transition:.18s}
        .fld:focus{border-color:var(--p-primary-light);box-shadow:0 0 0 3px rgba(124,58,237,.12)}
        .lbl{font-size:.78rem;font-weight:700;color:var(--p-muted);display:block;margin-bottom:.3rem}

        /* ===== PROGRESS RING ===== */
        .prog-ring{
            width:112px;height:112px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;
            background:conic-gradient(var(--p-primary) calc(var(--v)*1%), var(--p-border) 0);
        }
        .prog-ring .inner{
            width:82px;height:82px;border-radius:50%;background:var(--p-surface);
            display:grid;place-items:center;text-align:center;
        }
        .prog-ring b{font-size:1.3rem;font-weight:900;color:var(--p-primary-dark)}
        .prog-ring small{font-size:.66rem;color:var(--p-muted)}

        .check-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;
            padding:.55rem 0;border-bottom:1px solid var(--p-border)}
        .check-row:last-child{border-bottom:0}
        .check-row .k{display:flex;align-items:center;gap:.5rem;font-size:.85rem;font-weight:600}
        .check-row svg{width:17px;height:17px}
        .ok{color:var(--p-green)} .todo{color:var(--p-faint)}

        /* ===== BARS ===== */
        .bar-wrap{display:flex;flex-direction:column;gap:.9rem}
        .bar-row .bl{display:flex;justify-content:space-between;font-size:.8rem;font-weight:700;margin-bottom:.35rem}
        .bar{height:9px;background:var(--p-border);border-radius:999px;overflow:hidden}
        .bar>span{display:block;height:100%;width:0;border-radius:999px;transition:width .9s cubic-bezier(.2,.8,.2,1);
            background:linear-gradient(90deg,var(--p-primary),var(--p-primary-light))}

        /* ===== GALLERY / CHIPS ===== */
        .gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:.55rem}
        .gallery img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:11px;border:1px solid var(--p-border)}
        .tag-list{display:flex;flex-wrap:wrap;gap:.4rem}
        .tag{font-size:.76rem;font-weight:600;padding:.28rem .6rem;border-radius:999px;
            background:var(--p-surface-2);border:1px solid var(--p-border);color:var(--p-text)}

        /* ===== DOCS ===== */
        .doc{display:flex;align-items:center;gap:.9rem;padding:.8rem;border:1px solid var(--p-border);
            border-radius:12px;margin-bottom:.6rem;background:var(--p-surface-2)}
        .doc:last-child{margin-bottom:0}
        .fic{width:42px;height:42px;border-radius:10px;flex-shrink:0;display:grid;place-items:center;
            font-size:.66rem;font-weight:800;color:var(--p-primary);background:rgba(124,58,237,.1)}
        .doc .info{flex:1;min-width:0}
        .doc .info b{display:block;font-size:.86rem;font-weight:700}
        .doc .info small{font-size:.72rem;color:var(--p-muted)}
        .st{font-size:.7rem;font-weight:800;padding:.2rem .55rem;border-radius:999px}
        .st.ok{background:rgba(5,150,105,.13);color:var(--p-green)}
        .st.no{background:rgba(217,119,6,.14);color:var(--p-secondary)}

        /* ===== CALENDAR ===== */
        .cal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
        .cal-head b{font-size:1rem;font-weight:800}
        .cal-nav{display:flex;gap:.4rem}
        .cal-nav button{width:32px;height:32px;border-radius:9px;border:1px solid var(--p-border-strong);
            background:var(--p-surface);cursor:pointer;font-size:1.1rem;color:var(--p-text);line-height:1}
        .cal-nav button:hover{border-color:var(--p-primary-light);color:var(--p-primary)}
        .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:.3rem}
        .cal-dow{text-align:center;font-size:.7rem;font-weight:800;color:var(--p-faint);padding:.3rem 0}
        .cal-day{aspect-ratio:1;display:grid;place-items:center;border-radius:9px;font-size:.82rem;font-weight:600;
            border:1px solid transparent}
        .cal-day:not(.empty):hover{background:var(--p-surface-2);border-color:var(--p-border)}
        .cal-day.today{background:var(--p-primary);color:#fff;font-weight:800}
        .cal-day.busy{background:rgba(217,119,6,.16);color:var(--p-secondary);font-weight:800}
        .cal-day.off{background:rgba(220,38,38,.12);color:var(--p-red);text-decoration:line-through}
        .cal-day.empty{visibility:hidden}

        /* ===== SWITCH ===== */
        .dispo-row{display:flex;align-items:center;justify-content:space-between;padding:.55rem 0;
            border-bottom:1px solid var(--p-border)}
        .dispo-row:last-child{border-bottom:0}
        .switch{position:relative;display:inline-block;width:42px;height:24px}
        .switch input{opacity:0;width:0;height:0}
        .slider{position:absolute;inset:0;background:var(--p-border-strong);border-radius:999px;cursor:pointer;transition:.2s}
        .slider::before{content:"";position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;
            border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.25)}
        .switch input:checked+.slider{background:var(--p-primary)}
        .switch input:checked+.slider::before{transform:translateX(18px)}

        /* ===== BACKDROP (mobile) ===== */
        .pro-backdrop{display:none;position:fixed;inset:0;background:rgba(27,18,48,.5);z-index:55}
        .pro-backdrop.show{display:block}

        /* ===== RESPONSIVE ===== */
        @media(max-width:900px){
            .pro-side{position:fixed;left:0;top:0;transform:translateX(-100%);transition:transform .25s ease}
            .pro-side.open{transform:none}
            .pro-side-toggle{display:grid}
            .pro-panel{padding:1.1rem}
            .pro-topbar{padding:.75rem 1.1rem}
            .avail-pill{display:none}
            .pro-hero{min-height:200px;padding:1.5rem}
            .pro-hero .hero-bg{background-position:center center;}
        }
    </style>
</head>
<body>

<div class="artilo-pro" id="artiloPro">
    <div class="pro-shell">
        <!-- ===== SIDEBAR ===== -->
        <aside class="pro-side" id="proSide">
            <div class="pro-brand">
                <div class="logo">
                    @if ($logo)<img src="{{ $logo }}" alt="{{ $siteName }}">@else{{ strtoupper(substr($siteName,0,1)) }}@endif
                </div>
                <div class="brand-text">
                    <b>{{ $siteName }}</b>
                    <small>Partenaire Pro</small>
                </div>
            </div>

            <div class="pro-profile">
                <div class="pro-avatar">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
                <div class="who">
                    <b>{{ $user->name ?? 'Prestataire' }}</b>
                    <span class="pro-badge {{ $status }}"><span class="dot"></span>{{ $statusLabel }}</span>
                </div>
            </div>

            <nav class="pro-nav">
                <span class="cat">Pilotage</span>
                <button class="pro-link active" data-tab="dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                    Tableau de bord
                </button>
                <button class="pro-link" data-tab="missions">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Missions <span class="count">{{ count($missions) }}</span>
                </button>
                <button class="pro-link" data-tab="history">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>
                    Historique
                </button>

                <span class="cat">Finances</span>
                <button class="pro-link" data-tab="revenus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                    Revenus
                </button>
                <button class="pro-link" data-tab="stats">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg>
                    Statistiques
                </button>

                <span class="cat">Organisation</span>
                <button class="pro-link" data-tab="calendar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Calendrier
                </button>
                <button class="pro-link" data-tab="docs">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4a2 2 0 0 1 2-2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M14 2v6h6"/></svg>
                    Documents
                </button>
                <button class="pro-link" data-tab="profile">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    Mon profil
                </button>
            </nav>

            <div class="pro-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>
        <div class="pro-backdrop" id="proBackdrop"></div>

        <!-- ===== MAIN ===== -->
        <div class="pro-main">
            <!-- TOPBAR -->
            <div class="pro-topbar">
                <button class="pro-icon-btn pro-side-toggle" id="sideToggle" aria-label="Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
                <div class="pro-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" id="globalSearch" placeholder="Rechercher une mission, un client...">
                </div>
                <div class="top-spacer"></div>
                <span class="avail-pill {{ $dispo ? 'on' : 'off' }}">
                    <span class="dt"></span>{{ $dispo ? 'Disponible' : 'Indisponible' }}
                </span>
                <button class="pro-icon-btn" aria-label="Notifications">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="ping">3</span>
                </button>
                <a href="{{ route('artisan.profil.edit') }}" class="pro-icon-btn" title="Modifier mon profil">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                </a>
            </div>

            <!-- ===== DASHBOARD ===== -->
            <section class="pro-panel active" data-panel="dashboard">
                <div class="pro-hero">
                    <!-- Image de fond -->
                    <div class="hero-bg"></div>
                    <!-- Dégradé léger par-dessus -->
                    <div class="hero-overlay"></div>

                    <div>
                        <p class="eyebrow">Espace partenaire {{ $siteName }}</p>
                        <h1>Bonjour {{ $user->name ?? 'Prestataire' }}</h1>
                        <p>Voici votre poste de commande. Gérez vos missions attribuées, vos diagnostics, vos devis internes et vos paiements Mobile Money.</p>
                        <div class="cta">
                            <a href="{{ route('artisan.profil.edit') }}" class="btn solid">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                Modifier mon profil
                            </a>
                            <button class="btn ghost" data-goto="missions">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                Voir mes missions
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pro-stats" style="margin-top:1.25rem">
                    @foreach ($stats as $s)
                        <article class="stat {{ $s['tone'] }}">
                            <div class="ico">
                                @switch($s['icon'])
                                    @case('briefcase')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>@break
                                    @case('bolt')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></svg>@break
                                    @case('check')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>@break
                                    @case('star')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6.5 7 .9-5 4.8 1.2 7L12 18l-6.2 3.2L7 14.2l-5-4.8 7-.9z"/></svg>@break
                                @endswitch
                            </div>
                            <div class="num">{{ $s['value'] }}</div>
                            <div class="lab">{{ $s['label'] }}</div>
                            <div class="delta">{{ $s['delta'] }}</div>
                        </article>
                    @endforeach
                </div>

                <div class="grid-2" style="margin-top:1.25rem">
                    <div class="card">
                        <div class="sec-head">
                            <div><span class="eyebrow">À traiter</span><h3>Prochaines missions</h3></div>
                            <span class="chip">{{ count($missions) }} en file</span>
                        </div>
                        @foreach (array_slice($missions, 0, 2) as $m)
                            <div class="mission">
                                @if ($m['urgent'])<span class="urgent-flag">URGENT</span>@endif
                                <div class="top">
                                    <div>
                                        <span class="id">{{ $m['id'] }}</span>
                                        <h4>{{ $m['type'] }} — {{ $m['client'] }}</h4>
                                        <div class="meta">
                                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $m['zone'] }}</span>
                                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $m['date'] }}</span>
                                        </div>
                                    </div>
                                    <span class="budget">{{ $m['budget'] }}</span>
                                </div>
                            </div>
                        @endforeach
                        <button class="chip" style="border:0;cursor:pointer;margin-top:.4rem" data-goto="missions">Tout voir →</button>
                    </div>

                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Profil public</span><h3>Complétion</h3></div></div>
                        <div style="display:flex;align-items:center;gap:1.2rem;margin-bottom:1rem">
                            <div class="prog-ring" style="--v: {{ $profilePercent }}">
                                <div class="inner"><div><b>{{ $profilePercent }}%</b><br><small>complété</small></div></div>
                            </div>
                            <p style="color:var(--p-muted);font-size:.86rem;line-height:1.5">Un profil complet inspire confiance et vous attribue plus de missions.</p>
                        </div>
                        @foreach ($profileChecks as $label => $ready)
                            <div class="check-row">
                                <span class="k">
                                    @if ($ready)
                                        <svg class="ok" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
                                    @else
                                        <svg class="todo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                                    @endif
                                    {{ $label }}
                                </span>
                                <span class="{{ $ready ? 'ok' : 'todo' }}" style="font-size:.76rem;font-weight:800">{{ $ready ? 'OK' : 'À compléter' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ===== MISSIONS ===== -->
            <section class="pro-panel" data-panel="missions">
                <div class="card">
                    <div class="sec-head">
                        <div><span class="eyebrow">Partenariat {{ $siteName }}</span><h3>Missions attribuées</h3></div>
                        <span class="chip">Accepter / Refuser</span>
                    </div>
                    <div id="missionList">
                        @foreach ($missions as $m)
                            <div class="mission" data-search="{{ strtolower($m['id'].' '.$m['type'].' '.$m['client'].' '.$m['zone']) }}">
                                @if ($m['urgent'])<span class="urgent-flag">URGENT</span>@endif
                                <div class="top">
                                    <div>
                                        <span class="id">{{ $m['id'] }}</span>
                                        <h4>{{ $m['type'] }} — {{ $m['client'] }}</h4>
                                        <div class="meta">
                                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $m['zone'] }}</span>
                                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $m['date'] }}</span>
                                        </div>
                                    </div>
                                    <div style="text-align:right">
                                        <span class="state-tag {{ $m['state'] }}">
                                            {{ ['new'=>'Nouvelle','accepted'=>'Acceptée','progress'=>'En cours'][$m['state']] }}
                                        </span>
                                        <div class="budget" style="margin-top:.4rem">{{ $m['budget'] }}</div>
                                    </div>
                                </div>
                                <div class="acts">
                                    @if ($m['statut'] === 'affectee')
                                        <form action="{{ route('artisan.missions.accepter', $m['mission']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="m-btn accept"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>Accepter</button>
                                        </form>
                                        <form action="{{ route('artisan.missions.refuser', $m['mission']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="m-btn refuse" onclick="return confirm('Refuser cette mission ? Un autre prestataire sera recherché.')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>Refuser</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ===== HISTORIQUE ===== -->
            <section class="pro-panel" data-panel="history">
                <div class="card">
                    <div class="sec-head"><div><span class="eyebrow">Réalisé</span><h3>Historique</h3></div><span class="chip">{{ count($history) }} missions</span></div>
                    @foreach ($history as $h)
                        <div class="row">
                            <div class="l"><b>{{ $h['type'] }} · {{ $h['id'] }}</b><small>{{ $h['client'] }} — {{ $h['date'] }}</small></div>
                            <div style="text-align:right">
                                <div class="stars">{{ str_repeat('★', $h['note']).str_repeat('☆', 5 - $h['note']) }}</div>
                                <div class="amt in">{{ $h['amount'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ===== REVENUS ===== -->
            <section class="pro-panel" data-panel="revenus">
                <div class="pro-stats" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
                    <article class="stat green"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div><div class="num">485 000 F</div><div class="lab">Reçus ce mois</div><div class="delta">+18%</div></article>
                    <article class="stat amber"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div><div class="num">158 100 F</div><div class="lab">En attente</div><div class="delta">2 paiements</div></article>
                    <article class="stat primary"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div class="num">- 34 000 F</div><div class="lab">Commission (7%)</div><div class="delta">acompte + solde</div></article>
                </div>
                <div class="card" style="margin-top:1rem">
                    <div class="sec-head"><div><span class="eyebrow">Mobile Money</span><h3>Paiements reçus</h3></div><span class="chip">{{ $momoOperator ?: 'Moov · Yas' }}</span></div>
                    @foreach ($payments as $p)
                        <div class="row">
                            <div class="l"><b>{{ $p['label'] }}</b><small>{{ $p['method'] }} — {{ $p['date'] }}</small></div>
                            <span class="amt {{ $p['in'] ? 'in' : 'out' }}">{{ $p['amount'] }}</span>
                        </div>
                    @endforeach
                    @if ($momoNumber)
                        <div class="empty" style="margin-top:.4rem">Versements envoyés vers <b>{{ $momoNumber }}</b> ({{ $momoOperator ?: 'Mobile Money' }}).</div>
                    @endif
                </div>
            </section>

            <!-- ===== STATISTIQUES ===== -->
            <section class="pro-panel" data-panel="stats">
                <div class="pro-stats" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1rem">
                    <article class="stat primary"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg></div><div class="num">{{ $score }}/100</div><div class="lab">Score interne</div><div class="delta">{{ $niveau }}</div></article>
                    <article class="stat green"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg></div><div class="num">{{ $nbTerminees }}</div><div class="lab">Missions terminées</div><div class="delta">cumul</div></article>
                    <article class="stat amber"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6.5 7 .9-5 4.8 1.2 7L12 18l-6.2 3.2L7 14.2l-5-4.8 7-.9z"/></svg></div><div class="num">{{ $experience ?? '—' }}</div><div class="lab">Années d'expérience</div><div class="delta">{{ $profession ?? 'Métier' }}</div></article>
                </div>
                <div class="grid-2">
                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Répartition</span><h3>Par métier</h3></div></div>
                        <div class="bar-wrap">
                            @foreach ($repartition as $r)
                                <div class="bar-row">
                                    <div class="bl"><span>{{ $r['label'] }}</span><span>{{ $r['val'] }}%</span></div>
                                    <div class="bar"><span data-w="{{ $r['val'] }}"></span></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Notes</span><h3>Satisfaction</h3></div></div>
                        <div style="text-align:center;padding:.5rem 0 1rem">
                            <div style="font-size:3rem;font-weight:900;color:var(--p-primary-dark);line-height:1">{{ $rating > 0 ? number_format($rating, 1) : '—' }}</div>
                            <div class="stars" style="font-size:1.3rem">★★★★★</div>
                            <small style="color:var(--p-muted)">sur {{ count($history) + 9 }} avis</small>
                        </div>
                        <div class="bar-wrap">
                            @foreach ([['5★',72],['4★',20],['3★',6],['2★',2]] as $rt)
                                <div class="bar-row"><div class="bl"><span>{{ $rt[0] }}</span><span>{{ $rt[1] }}%</span></div><div class="bar"><span data-w="{{ $rt[1] }}"></span></div></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== CALENDRIER ===== -->
            <section class="pro-panel" data-panel="calendar">
                <div class="grid-2">
                    <div class="card">
                        <div class="cal-head">
                            <b id="calTitle">Calendrier</b>
                            <div class="cal-nav"><button id="calPrev">‹</button><button id="calNext">›</button></div>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                        <div style="display:flex;gap:1rem;margin-top:.9rem;font-size:.74rem;color:var(--p-muted);flex-wrap:wrap">
                            <span style="display:inline-flex;align-items:center;gap:.35rem"><i style="width:9px;height:9px;border-radius:50%;background:var(--p-primary);display:inline-block"></i>Aujourd'hui</span>
                            <span style="display:inline-flex;align-items:center;gap:.35rem"><i style="width:9px;height:9px;border-radius:50%;background:var(--p-secondary);display:inline-block"></i>Intervention</span>
                            <span style="display:inline-flex;align-items:center;gap:.35rem"><i style="width:9px;height:9px;border-radius:50%;background:var(--p-red);display:inline-block"></i>Indisponible</span>
                        </div>
                    </div>
                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Réglages</span><h3>Disponibilités</h3></div></div>
                        @foreach (['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $i => $day)
                            <div class="dispo-row">
                                <span style="font-weight:700;font-size:.9rem">{{ $day }}</span>
                                <label class="switch"><input type="checkbox" {{ $i < 5 ? 'checked' : '' }}><span class="slider"></span></label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ===== DOCUMENTS ===== -->
            <section class="pro-panel" data-panel="docs">
                <div class="card">
                    <div class="sec-head"><div><span class="eyebrow">Dossier</span><h3>Mes documents</h3></div>
                        <button class="m-btn devis"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Ajouter</button>
                    </div>
                    @foreach ($documents as $d)
                        <div class="doc">
                            <div class="fic">{{ $d['type'] }}</div>
                            <div class="info"><b>{{ $d['name'] }}</b><small>{{ $d['type'] }} · {{ $d['size'] }}</small></div>
                            <span class="st {{ $d['ok'] ? 'ok' : 'no' }}">{{ $d['ok'] ? 'Validé' : 'En attente' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ===== PROFIL ===== -->
            <section class="pro-panel" data-panel="profile">
                <div class="pro-hero">
                    <div class="hero-bg"></div>
                    <div class="hero-overlay"></div>
                    <div>
                        <p class="eyebrow">Mon profil public</p>
                        <h1>{{ $user->name ?? 'Prestataire' }}</h1>
                        <p>{{ ucfirst($profession ?? 'Métier à renseigner') }} · {{ $zone ?? 'Zone à renseigner' }}@if($company) · {{ $company }}@endif</p>
                        <div class="cta">
                            <a href="{{ route('artisan.profil.edit') }}" class="btn solid">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                Modifier mon profil
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid-2b" style="margin-top:1rem">
                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Coordonnées</span><h3>Informations</h3></div></div>
                        <div class="check-row"><span class="k">Métier</span><span style="font-weight:700">{{ $profession ?? '—' }}</span></div>
                        <div class="check-row"><span class="k">Niveau</span><span style="font-weight:700">{{ $niveau }}</span></div>
                        <div class="check-row"><span class="k">Zone d'intervention</span><span style="font-weight:700;text-align:right">{{ $zone ?? '—' }}</span></div>
                        <div class="check-row"><span class="k">Adresse</span><span style="font-weight:700;text-align:right">{{ $artisan?->address ?? '—' }}</span></div>
                        <div class="check-row"><span class="k">Mobile Money</span><span style="font-weight:700">{{ $momoNumber ? $momoNumber.' ('.($momoOperator ?: 'MoMo').')' : '—' }}</span></div>
                        <div class="check-row"><span class="k">Rayon max</span><span style="font-weight:700">{{ $artisan?->max_distance_km ? $artisan->max_distance_km.' km' : '—' }}</span></div>
                        <div class="check-row"><span class="k">Statut</span><span class="pro-badge {{ $status }}" style="background:rgba(124,58,237,.08)"><span class="dot"></span>{{ $statusLabel }}</span></div>
                    </div>
                    <div class="card">
                        <div class="sec-head"><div><span class="eyebrow">Progression</span><h3>Complétion</h3></div></div>
                        <div style="display:flex;justify-content:center;padding:.5rem 0 1rem">
                            <div class="prog-ring" style="--v: {{ $profilePercent }}"><div class="inner"><div><b>{{ $profilePercent }}%</b><br><small>complété</small></div></div></div>
                        </div>
                        @foreach ($profileChecks as $label => $ready)
                            <div class="check-row"><span class="k {{ $ready ? 'ok' : 'todo' }}">{{ $label }}</span><span class="{{ $ready ? 'ok' : 'todo' }}" style="font-weight:800;font-size:.78rem">{{ $ready ? 'OK' : 'À compléter' }}</span></div>
                        @endforeach
                    </div>
                </div>

                @if ($artisan?->description || $specialties->isNotEmpty() || $languages->isNotEmpty() || $certifs->isNotEmpty())
                    <div class="card" style="margin-top:1rem">
                        <div class="sec-head"><div><span class="eyebrow">Présentation</span><h3>À propos</h3></div></div>
                        @if ($artisan?->description)
                            <p style="font-size:.9rem;line-height:1.7;color:var(--p-text)">{{ $artisan->description }}</p>
                        @endif
                        @if ($specialties->isNotEmpty())
                            <label class="lbl" style="margin-top:1rem">Spécialités</label>
                            <div class="tag-list">@foreach ($specialties as $sp)<span class="tag">{{ $sp }}</span>@endforeach</div>
                        @endif
                        @if ($languages->isNotEmpty())
                            <label class="lbl" style="margin-top:1rem">Langues</label>
                            <div class="tag-list">@foreach ($languages as $lg)<span class="tag">{{ $lg }}</span>@endforeach</div>
                        @endif
                        @if ($certifs->isNotEmpty())
                            <label class="lbl" style="margin-top:1rem">Certifications</label>
                            <div class="tag-list">@foreach ($certifs as $ct)<span class="tag">{{ $ct }}</span>@endforeach</div>
                        @endif
                    </div>
                @endif

                @if ($gallery->isNotEmpty())
                    <div class="card" style="margin-top:1rem">
                        <div class="sec-head"><div><span class="eyebrow">Réalisations</span><h3>Galerie photos</h3></div><span class="chip">{{ $gallery->count() }} photos</span></div>
                        <div class="gallery">
                            @foreach ($gallery as $photo)
                                <img src="{{ $photo }}" alt="Réalisation de {{ $user->name ?? 'prestataire' }}" loading="lazy">
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

        </div>
    </div>

    <script>
        (function() {
            const root = document.getElementById('artiloPro');

            // Navigation
            const links = root.querySelectorAll('.pro-link[data-tab]');
            const panels = root.querySelectorAll('.pro-panel');

            function activate(tab) {
                links.forEach(l => l.classList.toggle('active', l.dataset.tab === tab));
                panels.forEach(p => p.classList.toggle('active', p.dataset.panel === tab));
                if (tab === 'stats') animateBars();
                closeSide();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            links.forEach(l => l.addEventListener('click', () => activate(l.dataset.tab)));
            root.querySelectorAll('[data-goto]').forEach(b => b.addEventListener('click', () => activate(b.dataset.goto)));

            // Bars animation
            function animateBars() {
                root.querySelectorAll('.bar > span[data-w]').forEach(s => {
                    requestAnimationFrame(() => { s.style.width = s.dataset.w + '%'; });
                });
            }

            // Sidebar mobile
            const side = document.getElementById('proSide');
            const backdrop = document.getElementById('proBackdrop');
            const toggle = document.getElementById('sideToggle');

            function closeSide() {
                side.classList.remove('open');
                backdrop.classList.remove('show');
            }

            toggle?.addEventListener('click', () => {
                side.classList.toggle('open');
                backdrop.classList.toggle('show');
            });
            backdrop?.addEventListener('click', closeSide);

            // Search
            const search = document.getElementById('globalSearch');
            search?.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                if (q && !document.querySelector('.pro-panel[data-panel="missions"]').classList.contains('active')) {
                    activate('missions');
                }
                root.querySelectorAll('#missionList .mission').forEach(m => {
                    m.style.display = m.dataset.search.includes(q) ? '' : 'none';
                });
            });

            // Toggle disponibilités
            root.querySelectorAll('.switch input').forEach(inp => {
                inp.addEventListener('change', function() {
                    this.closest('.dispo-row').style.opacity = this.checked ? '1' : '.55';
                });
            });

            // Calendar
            const grid = document.getElementById('calGrid');
            const title = document.getElementById('calTitle');
            let view = new Date();
            const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            const busyDays = [12, 15, 18, 22];
            const offDays = [20, 21];

            function renderCal() {
                const y = view.getFullYear(), m = view.getMonth();
                title.textContent = months[m] + ' ' + y;
                grid.innerHTML = '';
                ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach(d => {
                    const el = document.createElement('div');
                    el.className = 'cal-dow';
                    el.textContent = d;
                    grid.appendChild(el);
                });
                let first = new Date(y, m, 1).getDay();
                first = first === 0 ? 6 : first - 1;
                const days = new Date(y, m + 1, 0).getDate();
                const today = new Date();
                for (let i = 0; i < first; i++) {
                    const e = document.createElement('div');
                    e.className = 'cal-day empty';
                    grid.appendChild(e);
                }
                for (let d = 1; d <= days; d++) {
                    const el = document.createElement('div');
                    el.className = 'cal-day';
                    el.textContent = d;
                    if (d === today.getDate() && m === today.getMonth() && y === today.getFullYear()) el.classList.add('today');
                    if (busyDays.includes(d)) el.classList.add('busy');
                    if (offDays.includes(d)) el.classList.add('off');
                    grid.appendChild(el);
                }
            }

            document.getElementById('calPrev')?.addEventListener('click', () => { view.setMonth(view.getMonth() - 1); renderCal(); });
            document.getElementById('calNext')?.addEventListener('click', () => { view.setMonth(view.getMonth() + 1); renderCal(); });
            renderCal();
        })();
    </script>
</div>

</body>
</html>