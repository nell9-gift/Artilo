@php
/* ============================================================================
   ARTILO — ESPACE PARTICULIER (design "verre" complet)
   Rebranché sur les vraies données du projet : Setting (DB), Auth::user(),
   Mission (particulier_id, metier_requis, statut, artisan.user).
   ----------------------------------------------------------------------------
   Le design (CSS/HTML/JS) est repris à l'identique de ta première version.
   Seule la partie "données" a été corrigée pour coller à ton vrai schéma :
   - Les relations `devis` et `paiement` ont été retirées (aucun modèle
     Devis/Paiement n'existe encore dans le projet) : ces sections affichent
     un état vide propre plutôt que de planter.
   - Les statuts utilisés correspondent désormais à Mission::STATUTS.
   ========================================================================== */

use App\Models\Setting;
use App\Models\Mission;

/* ------------------------------------------------------------------ *
 * 1) HELPER settings — lit la vraie table `settings`                  *
 *    Retombe sur des valeurs par défaut si une clé n'existe pas.      *
 * ------------------------------------------------------------------ */
function settings_all(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $defaults = [
        'color_primary'         => '#7C3AED',
        'color_primary_dark'    => '#6D28D9',
        'color_primary_900'     => '#2E1065',
        'color_primary_800'     => '#4C1D95',
        'color_primary_light'   => '#A78BFA',
        'color_secondary'       => '#D97706',
        'color_secondary_light' => '#F59E0B',
        'color_bg'              => '#FFFFFF',
        'color_bg_2'            => '#FAF8FF',
        'color_text'            => '#2E1065',
        'color_muted'           => '#6B5B95',

        'logo'                    => 'images/logo.jpeg',
        'trade_image_plomberie'   => 'images/plomberie.jfif',
        'trade_image_electricite' => 'images/electricité.jfif',
        'trade_image_maconnerie'  => 'images/maçonnerie.jfif',
        'trade_image_menuiserie'  => 'images/menuiserie.jfif',
        'image_maison'            => 'images/maison.jfif',
        'image_outils'            => 'images/outils.jfif',

        'site_name'     => 'Artilo',
        'contact_phone' => '+228 92 89 37 97',
        'contact_email' => 'pacomedzah720@gmail.com',
        'contact_city'  => 'Lomé, Togo',
    ];

    $db = [];
    try {
        $db = Setting::whereIn('key', array_keys($defaults))->pluck('value', 'key')->toArray();
    } catch (\Throwable $e) {
        // table pas encore migrée / vide : on garde les défauts, pas de crash
    }
    return $cache = array_merge($defaults, $db);
}

/* Clés d'images à résoudre via asset() (chemins stockés en DB = relatifs) */
const IMG_KEYS = ['logo','trade_image_plomberie','trade_image_electricite','trade_image_maconnerie','trade_image_menuiserie','image_maison','image_outils'];

function setting(string $key, string $fallback = ''): string {
    $val = settings_all()[$key] ?? $fallback;
    if ($val && in_array($key, IMG_KEYS, true)) {
        return asset($val);
    }
    return $val;
}
// Note : on utilise la fonction e() native de Laravel (déjà déclarée globalement
// dans vendor/laravel/framework), donc on ne la redéclare pas ici.

/* Palette exposée au CSS */
$C = [
    'primary'    => setting('color_primary',       '#7C3AED'),
    'p_dark'     => setting('color_primary_dark',  '#6D28D9'),
    'p_900'      => setting('color_primary_900',   '#2E1065'),
    'p_800'      => setting('color_primary_800',   '#4C1D95'),
    'p_light'    => setting('color_primary_light', '#A78BFA'),
    'secondary'  => setting('color_secondary',     '#D97706'),
    'sec_light'  => setting('color_secondary_light','#F59E0B'),
    'bg'         => setting('color_bg',            '#FFFFFF'),
    'bg2'        => setting('color_bg_2',          '#FAF8FF'),
    'text'       => setting('color_text',          '#2E1065'),
    'muted'      => setting('color_muted',         '#6B5B95'),
];
$SITE = setting('site_name', 'Artilo');

/* ------------------------------------------------------------------ *
 * 2) UTILISATEUR CONNECTÉ (le vrai, via Auth)                         *
 * ------------------------------------------------------------------ */
$authUser = Auth::user();
$user = [
    'name'    => $authUser->name,
    'email'   => $authUser->email,
    'city'    => setting('contact_city', 'Lomé, Togo'),
    'initial' => strtoupper(substr($authUser->name, 0, 1)),
    'since'   => 'Membre depuis '.$authUser->created_at->translatedFormat('F Y'),
];

/* ------------------------------------------------------------------ *
 * 3) DONNÉES RÉELLES — missions du particulier connecté               *
 *    Libellés alignés sur les vrais statuts définis dans              *
 *    App\Models\Mission::STATUTS.                                     *
 * ------------------------------------------------------------------ */
$statutLabels = [
    'en_attente'                  => "En recherche d'artisan",
    'affectee'                    => 'Artisan proposé',
    'acceptee'                    => 'Artisan en route',
    'diagnostic_effectue'         => 'Diagnostic effectué',
    'devis_en_attente_validation' => 'Devis en validation',
    'devis_valide'                => 'Devis validé',
    'devis_envoye'                => 'Devis reçu',
    'devis_refuse'                => 'Devis refusé',
    'acompte_regle'                => 'Acompte réglé',
    'en_cours'                    => 'Travaux en cours',
    'terminee_prestataire'        => 'Travaux terminés',
    'validee_client'              => 'Travaux validés',
    'solde_regle'                 => 'Solde réglé',
    'payee'                       => 'Payée',
    'annulee'                     => 'Annulée',
];

function tradeImageKey(string $metier): string {
    return match (strtolower($metier)) {
        'plomberie'                 => 'trade_image_plomberie',
        'électricité', 'electricite'=> 'trade_image_electricite',
        'maçonnerie', 'maconnerie'  => 'trade_image_maconnerie',
        'menuiserie'                => 'trade_image_menuiserie',
        default                     => 'image_outils',
    };
}

function statutClass(string $statutKey): string {
    return match ($statutKey) {
        'en_cours', 'affectee', 'acceptee', 'diagnostic_effectue', 'devis_en_attente_validation', 'devis_envoye' => 'st-blue',
        'solde_regle', 'payee', 'validee_client', 'devis_valide', 'acompte_regle'                                => 'st-green',
        'en_attente'                                                                                              => 'st-amber',
        'annulee', 'devis_refuse'                                                                                 => 'st-red',
        default                                                                                                    => 'st-gray',
    };
}

// 🔧 On ne charge plus 'devis' ni 'paiement' : ces modèles n'existent pas
// encore dans le projet. Seule la relation artisan.user est utile ici.
$missionsDb = Mission::where('particulier_id', $authUser->id)
    ->with(['artisan.user'])
    ->latest()
    ->get();

$stats = [
    ['label' => 'Demandes envoyées', 'value' => (string) $missionsDb->count(), 'unit' => 'missions', 'delta' => '', 'up' => true, 'icon' => 'tasks'],
    ['label' => 'Missions actives',  'value' => (string) $missionsDb->whereIn('statut', ['affectee','acceptee','diagnostic_effectue','devis_en_attente_validation','devis_valide','devis_envoye','acompte_regle','en_cours'])->count(), 'unit' => 'en cours', 'delta' => '', 'up' => true, 'icon' => 'wallet'],
    ['label' => 'Devis à valider',   'value' => (string) $missionsDb->where('statut', 'devis_en_attente_validation')->count(), 'unit' => 'en attente', 'delta' => '', 'up' => true, 'icon' => 'quote'],
    // 🚧 Module paiements pas encore développé : on affiche 0 en attendant.
    ['label' => 'Total réglé',       'value' => '0', 'unit' => 'FCFA', 'delta' => '', 'up' => false, 'icon' => 'chart'],
];

$missions = $missionsDb->map(function ($m) use ($statutLabels) {
    return [
        'titre'        => ucfirst($m->metier_requis).' — Mission #'.$m->id,
        'artisan'      => $m->artisan->user->name ?? 'Non affecté pour le moment',
        'metier'       => ucfirst($m->metier_requis),
        'statut'       => $m->statut,
        'statut_label' => $statutLabels[$m->statut] ?? ucfirst(str_replace('_', ' ', $m->statut)),
        'avance'       => match ($m->statut) {
            'payee', 'solde_regle', 'validee_client'                                     => 100,
            'terminee_prestataire', 'en_cours'                                            => 60,
            'devis_valide', 'devis_envoye', 'acompte_regle'                               => 40,
            'devis_en_attente_validation', 'diagnostic_effectue', 'acceptee', 'affectee'  => 20,
            default                                                                        => 5,
        },
        'date' => $m->created_at->translatedFormat('d M Y'),
        'img'  => tradeImageKey($m->metier_requis),
    ];
})->all();

// 🚧 Devis et paiements : fonctionnalités pas encore développées (aucun
// modèle Devis/Paiement dans le projet pour l'instant). On affiche un état
// vide propre plutôt que de planter ou d'inventer des données.
$devis = [];
$paiements = [];

$historique = $missionsDb->whereIn('statut', ['payee', 'solde_regle', 'annulee'])->map(function ($m) use ($statutLabels) {
    return [
        'action' => $statutLabels[$m->statut] ?? $m->statut,
        'detail' => ucfirst($m->metier_requis).' — '.($m->artisan->user->name ?? 'Non affecté'),
        'date'   => $m->updated_at->translatedFormat('d M Y'),
    ];
})->values()->all();

// Ces fonctionnalités n'ont pas encore de modèle dédié dans le projet :
// on affiche un état "vide" propre plutôt que de fausses données.
$messages = [];
$notifications = [];
$documents = [];
$avis = [];

// 🚧 Mini-graphique dépenses : pas encore de modèle Paiement en base.
// On affiche un graphique à zéro en attendant que le module soit développé.
$chartLabels = [];
$chart = [];
for ($i = 6; $i >= 0; $i--) {
    $month = now()->subMonths($i);
    $chartLabels[] = ucfirst($month->translatedFormat('M'));
    $chart[] = 0;
}
$chartMax = max($chart) ?: 1;

/* Menu latéral (inchangé) */
$menu = [
    ['id' => 'dashboard',    'label' => 'Tableau de bord', 'icon' => 'grid'],
    ['id' => 'nouvelle',     'label' => 'Nouvelle demande','icon' => 'plus'],
    ['id' => 'missions',     'label' => 'Mes missions',    'icon' => 'tasks'],
    ['id' => 'devis',        'label' => 'Mes devis',       'icon' => 'quote'],
    ['id' => 'paiements',    'label' => 'Paiements',       'icon' => 'wallet'],
    ['id' => 'calendrier',   'label' => 'Calendrier',      'icon' => 'calendar'],
    ['id' => 'documents',    'label' => 'Documents',       'icon' => 'doc'],
    ['id' => 'messages',     'label' => 'Messages',        'icon' => 'chat'],
    ['id' => 'notifications','label' => 'Notifications',   'icon' => 'bell'],
    ['id' => 'historique',   'label' => 'Historique',      'icon' => 'clock'],
    ['id' => 'avis',         'label' => 'Avis',            'icon' => 'star'],
    ['id' => 'support',      'label' => 'Support',         'icon' => 'help'],
    ['id' => 'profil',       'label' => 'Mon profil',      'icon' => 'user'],
];

/* Icônes SVG inline (stroke) — inchangé */
function icon(string $n): string {
    $p = [
        'grid'    => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
        'plus'    => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'tasks'   => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'quote'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>',
        'wallet'  => '<path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><circle cx="17" cy="14" r="1"/>',
        'calendar'=> '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'doc'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
        'chat'    => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'bell'    => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'clock'   => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/>',
        'star'    => '<polygon points="12 2 15.1 8.6 22 9.3 17 14 18.2 21 12 17.6 5.8 21 7 14 2 9.3 8.9 8.6 12 2"/>',
        'help'    => '<circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12" y2="17"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'chart'   => '<line x1="4" y1="20" x2="20" y2="20"/><rect x="6" y="11" width="3" height="7"/><rect x="11" y="7" width="3" height="11"/><rect x="16" y="14" width="3" height="4"/>',
        'search'  => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'arrow'   => '<line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>',
        'download'=> '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        'phone'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/>',
        'mail'    => '<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/>',
        'menu'    => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
        'pin'     => '<path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">'.($p[$n] ?? '').'</svg>';
}
@endphp

<x-app-layout>
<div class="al-dash">
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');

.al-dash{
  --primary:<?= e($C['primary']) ?>;
  --p-dark:<?= e($C['p_dark']) ?>;
  --p-900:<?= e($C['p_900']) ?>;
  --p-800:<?= e($C['p_800']) ?>;
  --p-light:<?= e($C['p_light']) ?>;
  --secondary:<?= e($C['secondary']) ?>;
  --sec-light:<?= e($C['sec_light']) ?>;
  --bg:<?= e($C['bg']) ?>;
  --bg2:<?= e($C['bg2']) ?>;
  --text:<?= e($C['text']) ?>;
  --muted:<?= e($C['muted']) ?>;

  --glass: rgba(255,255,255,.55);
  --glass-strong: rgba(255,255,255,.72);
  --glass-brd: rgba(255,255,255,.65);
  --shadow: 0 20px 50px -20px rgba(46,16,101,.45);
  --shadow-sm: 0 8px 24px -14px rgba(46,16,101,.5);
  --radius: 22px;
  --sidebar: 268px;

  position:relative;
  font-family:'Inter',system-ui,sans-serif;
  color:var(--text);
  background:
    radial-gradient(1200px 700px at 82% -10%, rgba(124,58,237,.22), transparent 60%),
    radial-gradient(900px 600px at -5% 15%, rgba(217,119,6,.14), transparent 55%),
    radial-gradient(1000px 800px at 60% 110%, rgba(167,139,250,.20), transparent 60%),
    linear-gradient(160deg, var(--bg2) 0%, #F3EEFF 45%, #FBF7FF 100%);
  -webkit-font-smoothing:antialiased;
}
.al-dash *{box-sizing:border-box}
.al-dash h1,.al-dash h2,.al-dash h3,.al-dash h4,.al-dash .font-head{font-family:'Sora',sans-serif;letter-spacing:-.02em;margin:0}
.al-dash a{color:inherit;text-decoration:none}
.al-dash svg{width:20px;height:20px;display:block}
.al-dash ::selection{background:var(--p-light);color:#fff}

/* floating orbs (déco douce) */
.al-dash .orb{position:absolute;border-radius:50%;filter:blur(60px);opacity:.5;z-index:0;pointer-events:none;animation:al-float 16s ease-in-out infinite}
.al-dash .orb.o1{width:420px;height:420px;background:radial-gradient(circle,rgba(124,58,237,.55),transparent 70%);top:-140px;right:-80px}
.al-dash .orb.o2{width:360px;height:360px;background:radial-gradient(circle,rgba(245,158,11,.4),transparent 70%);bottom:-120px;left:12%;animation-delay:-6s}
@keyframes al-float{0%,100%{transform:translateY(0) translateX(0)}50%{transform:translateY(-34px) translateX(20px)}}

/* ---------- LAYOUT ---------- */
.al-dash .shell{position:relative;z-index:1;display:flex;min-height:100vh}

/* ---------- SIDEBAR ---------- */
.al-dash .sidebar{
  width:var(--sidebar);flex-shrink:0;position:sticky;top:0;height:100vh;
  display:flex;flex-direction:column;gap:6px;padding:22px 16px;
  background:linear-gradient(185deg, rgba(46,16,101,.92), rgba(76,29,149,.88));
  backdrop-filter:blur(18px);
  border-right:1px solid rgba(255,255,255,.08);
  color:#EDE7FF;
}
.al-dash .brand{display:flex;align-items:center;gap:12px;padding:6px 10px 18px}
.al-dash .brand .logo{width:44px;height:44px;border-radius:13px;object-fit:cover;
  box-shadow:0 8px 20px -6px rgba(0,0,0,.5);border:1.5px solid rgba(255,255,255,.25);background:#fff}
.al-dash .brand b{font-family:'Sora';font-size:20px;color:#fff;letter-spacing:-.03em}
.al-dash .brand span{display:block;font-size:11px;color:var(--p-light);font-weight:500;letter-spacing:.14em;text-transform:uppercase}
.al-dash .nav{display:flex;flex-direction:column;gap:3px;overflow-y:auto;flex:1;padding-right:4px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.25) transparent}
.al-dash .nav::-webkit-scrollbar{width:6px}
.al-dash .nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.18);border-radius:10px}
.al-dash .nav-item{
  display:flex;align-items:center;gap:13px;padding:11px 13px;border-radius:13px;
  color:#CFC3F5;font-size:14.5px;font-weight:500;cursor:pointer;position:relative;
  transition:.22s cubic-bezier(.4,0,.2,1);border:1px solid transparent;
}
.al-dash .nav-item svg{width:19px;height:19px;opacity:.85;transition:.22s}
.al-dash .nav-item:hover{background:rgba(255,255,255,.08);color:#fff;transform:translateX(3px)}
.al-dash .nav-item.active{
  background:linear-gradient(100deg, rgba(167,139,250,.28), rgba(124,58,237,.12));
  color:#fff;border-color:rgba(167,139,250,.35);
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.05), 0 8px 20px -12px rgba(0,0,0,.6);
}
.al-dash .nav-item.active::before{content:"";position:absolute;left:-16px;top:50%;transform:translateY(-50%);
  width:4px;height:22px;border-radius:4px;background:linear-gradient(var(--sec-light),var(--secondary));box-shadow:0 0 12px var(--secondary)}
.al-dash .nav-item.active svg{opacity:1}
.al-dash .nav-badge{margin-left:auto;background:var(--secondary);color:#fff;font-size:11px;font-weight:700;
  min-width:20px;height:20px;padding:0 6px;border-radius:20px;display:grid;place-items:center}
.al-dash .nav-sep{height:1px;background:rgba(255,255,255,.09);margin:10px 6px}
.al-dash .logout{
  display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:13px;cursor:pointer;
  color:#FBD9C6;font-weight:600;font-size:14.5px;
  background:rgba(217,119,6,.14);border:1px solid rgba(245,158,11,.28);transition:.22s;
}
.al-dash .logout:hover{background:rgba(217,119,6,.28);color:#fff;transform:translateY(-1px)}

/* ---------- MAIN ---------- */
.al-dash .main{flex:1;min-width:0;display:flex;flex-direction:column}
.al-dash .topbar{
  position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:16px;
  padding:16px 26px;background:var(--glass);backdrop-filter:blur(16px);
  border-bottom:1px solid var(--glass-brd);
}
.al-dash .burger{display:none;background:var(--glass-strong);border:1px solid var(--glass-brd);border-radius:12px;
  width:42px;height:42px;place-items:center;color:var(--p-800);cursor:pointer}
.al-dash .page-title h1{font-size:22px;line-height:1.1}
.al-dash .page-title p{font-size:13px;color:var(--muted);margin-top:2px}
.al-dash .search{
  margin-left:auto;display:flex;align-items:center;gap:10px;background:var(--glass-strong);
  border:1px solid var(--glass-brd);border-radius:14px;padding:10px 15px;min-width:220px;max-width:340px;flex:1;
  color:var(--muted);transition:.2s;
}
.al-dash .search:focus-within{box-shadow:0 0 0 3px rgba(124,58,237,.18);border-color:var(--p-light)}
.al-dash .search svg{width:18px;height:18px}
.al-dash .search input{border:none;background:none;outline:none;font-size:14px;color:var(--text);width:100%;font-family:inherit}
.al-dash .search input::placeholder{color:var(--muted)}
.al-dash .icon-btn{position:relative;width:44px;height:44px;flex-shrink:0;display:grid;place-items:center;
  background:var(--glass-strong);border:1px solid var(--glass-brd);border-radius:14px;color:var(--p-800);cursor:pointer;transition:.2s}
.al-dash .icon-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm);color:var(--primary)}
.al-dash .icon-btn .dot{position:absolute;top:9px;right:10px;width:8px;height:8px;border-radius:50%;background:var(--secondary);border:2px solid #fff}
.al-dash .avatar-btn{display:flex;align-items:center;gap:10px;background:var(--glass-strong);border:1px solid var(--glass-brd);
  border-radius:14px;padding:6px 12px 6px 6px;cursor:pointer;transition:.2s}
.al-dash .avatar-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm)}
.al-dash .avatar-circle{width:34px;height:34px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,var(--primary),var(--p-dark));color:#fff;font-family:'Sora';font-weight:700;font-size:14px}
.al-dash .avatar-btn .nm{font-size:13.5px;font-weight:600;line-height:1.1}
.al-dash .avatar-btn .rl{font-size:11px;color:var(--muted)}
@media(max-width:640px){.al-dash .avatar-btn .who{display:none}}

.al-dash .content{padding:26px;max-width:1320px;width:100%;margin:0 auto}

/* pages */
.al-dash .page{display:none;animation:al-fade .5s cubic-bezier(.2,.8,.2,1)}
.al-dash .page.show{display:block}
@keyframes al-fade{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* glass card */
.al-dash .card{
  background:var(--glass);backdrop-filter:blur(16px);
  border:1px solid var(--glass-brd);border-radius:var(--radius);
  box-shadow:var(--shadow);
}
.al-dash .card.solid{background:var(--glass-strong)}
.al-dash .reveal{opacity:0;transform:translateY(22px)}
.al-dash .reveal.in{opacity:1;transform:none;transition:.7s cubic-bezier(.2,.8,.2,1)}

/* ---- KPI grid ---- */
.al-dash .kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px}
@media(max-width:1050px){.al-dash .kpi-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.al-dash .kpi-grid{grid-template-columns:1fr}}
.al-dash .kpi{padding:20px;position:relative;overflow:hidden;transition:.3s}
.al-dash .kpi:hover{transform:translateY(-6px);box-shadow:0 30px 60px -25px rgba(46,16,101,.55)}
.al-dash .kpi::after{content:"";position:absolute;inset:0;background:linear-gradient(130deg,rgba(255,255,255,.4),transparent 45%);pointer-events:none}
.al-dash .kpi .k-ic{width:46px;height:46px;border-radius:14px;display:grid;place-items:center;color:#fff;margin-bottom:16px;
  background:linear-gradient(135deg,var(--primary),var(--p-dark));box-shadow:0 10px 22px -8px rgba(124,58,237,.7)}
.al-dash .kpi:nth-child(2) .k-ic{background:linear-gradient(135deg,#3B82F6,#1D4ED8);box-shadow:0 10px 22px -8px rgba(37,99,235,.6)}
.al-dash .kpi:nth-child(3) .k-ic{background:linear-gradient(135deg,var(--sec-light),var(--secondary));box-shadow:0 10px 22px -8px rgba(217,119,6,.6)}
.al-dash .kpi:nth-child(4) .k-ic{background:linear-gradient(135deg,#EC4899,#BE185D);box-shadow:0 10px 22px -8px rgba(236,72,153,.5)}
.al-dash .kpi .k-lab{font-size:13px;color:var(--muted);font-weight:500}
.al-dash .kpi .k-val{font-family:'Sora';font-size:27px;font-weight:700;margin-top:4px;line-height:1}
.al-dash .kpi .k-unit{font-size:12px;color:var(--muted);font-weight:600}
.al-dash .kpi .k-delta{position:absolute;top:20px;right:20px;font-size:12px;font-weight:700;padding:4px 9px;border-radius:20px}
.al-dash .k-delta.up{background:rgba(16,185,129,.14);color:#047857}
.al-dash .k-delta.down{background:rgba(239,68,68,.13);color:#b91c1c}

/* section header */
.al-dash .sec-head{display:flex;align-items:center;justify-content:space-between;margin:0 0 16px}
.al-dash .sec-head h2{font-size:18px}
.al-dash .sec-head .link{font-size:13px;color:var(--primary);font-weight:600;display:flex;align-items:center;gap:5px;cursor:pointer}
.al-dash .sec-head .link svg{width:15px;height:15px}

/* layout 2 cols */
.al-dash .grid-2{display:grid;grid-template-columns:1.6fr 1fr;gap:18px;align-items:start}
@media(max-width:960px){.al-dash .grid-2{grid-template-columns:1fr}}

/* chart */
.al-dash .chart-card{padding:22px}
.al-dash .bars{display:flex;align-items:flex-end;gap:12px;height:170px;margin-top:20px}
.al-dash .bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;height:100%;justify-content:flex-end}
.al-dash .bar{width:100%;max-width:34px;border-radius:9px 9px 4px 4px;background:linear-gradient(var(--p-light),var(--primary));
  box-shadow:0 6px 14px -6px rgba(124,58,237,.6);transition:height 1s cubic-bezier(.2,.8,.2,1);position:relative}
.al-dash .bar.hl{background:linear-gradient(var(--sec-light),var(--secondary))}
.al-dash .bar-wrap span{font-size:11px;color:var(--muted);font-weight:600}

/* mission cards */
.al-dash .mission{display:flex;gap:14px;padding:15px;border-radius:16px;background:var(--glass-strong);
  border:1px solid var(--glass-brd);transition:.25s;margin-bottom:12px}
.al-dash .mission:hover{transform:translateX(4px);box-shadow:var(--shadow-sm)}
.al-dash .mission .thumb{width:60px;height:60px;border-radius:13px;object-fit:cover;flex-shrink:0;background:var(--p-light)}
.al-dash .mission .m-body{flex:1;min-width:0}
.al-dash .mission .m-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
.al-dash .mission h4{font-size:15px}
.al-dash .mission .m-sub{font-size:12.5px;color:var(--muted);margin:3px 0 9px}
.al-dash .progress{height:7px;border-radius:10px;background:rgba(124,58,237,.14);overflow:hidden}
.al-dash .progress i{display:block;height:100%;border-radius:10px;background:linear-gradient(90deg,var(--primary),var(--sec-light));width:0;transition:width 1.1s cubic-bezier(.2,.8,.2,1)}
.al-dash .mission .m-foot{display:flex;justify-content:space-between;font-size:11.5px;color:var(--muted);margin-top:7px;font-weight:500}

/* badges statut */
.al-dash .badge{font-size:11.5px;font-weight:700;padding:4px 11px;border-radius:20px;white-space:nowrap}
.al-dash .st-blue{background:rgba(59,130,246,.15);color:#1d4ed8}
.al-dash .st-green{background:rgba(16,185,129,.15);color:#047857}
.al-dash .st-amber{background:rgba(217,119,6,.16);color:#b45309}
.al-dash .st-red{background:rgba(239,68,68,.14);color:#b91c1c}
.al-dash .st-gray{background:rgba(107,91,149,.15);color:var(--muted)}

/* table */
.al-dash .tbl-card{padding:8px 6px}
.al-dash .tbl{width:100%;border-collapse:collapse}
.al-dash .tbl th{text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);
  font-weight:700;padding:14px 16px}
.al-dash .tbl td{padding:14px 16px;font-size:14px;border-top:1px solid rgba(124,58,237,.08)}
.al-dash .tbl tr{transition:.15s}
.al-dash .tbl tbody tr:hover{background:rgba(124,58,237,.05)}
.al-dash .tbl .ref{font-family:'Sora';font-weight:600;font-size:13px}
.al-dash .amount{font-family:'Sora';font-weight:700}
.al-dash .amount.in{color:#047857}.al-dash .amount.out{color:var(--p-800)}
.al-dash .tbl-scroll{overflow-x:auto}

/* list items generic */
.al-dash .li{display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:14px;transition:.2s}
.al-dash .li:hover{background:rgba(124,58,237,.05)}
.al-dash .li + .li{border-top:1px solid rgba(124,58,237,.07)}
.al-dash .li .li-ic{width:42px;height:42px;border-radius:12px;flex-shrink:0;display:grid;place-items:center;
  background:linear-gradient(135deg,rgba(124,58,237,.15),rgba(167,139,250,.12));color:var(--primary)}
.al-dash .li .li-ic svg{width:20px;height:20px}
.al-dash .li .li-main{flex:1;min-width:0}
.al-dash .li .li-main b{font-size:14px;font-weight:600;display:block}
.al-dash .li .li-main small{font-size:12.5px;color:var(--muted)}
.al-dash .li .li-time{font-size:12px;color:var(--muted);white-space:nowrap}
.al-dash .li .unread{width:9px;height:9px;border-radius:50%;background:var(--secondary);flex-shrink:0}
.al-dash .n-success .li-ic{background:rgba(16,185,129,.14);color:#047857}
.al-dash .n-warning .li-ic{background:rgba(217,119,6,.16);color:#b45309}
.al-dash .n-info .li-ic{background:rgba(59,130,246,.14);color:#1d4ed8}

/* empty state */
.al-dash .empty{text-align:center;padding:34px 20px;color:var(--muted);font-size:13.5px;border:1.5px dashed rgba(124,58,237,.22);border-radius:16px;background:rgba(124,58,237,.04)}

/* buttons */
.al-dash .btn{display:inline-flex;align-items:center;gap:8px;font-family:'Sora';font-weight:600;font-size:14px;
  padding:12px 20px;border-radius:14px;border:none;cursor:pointer;transition:.22s}
.al-dash .btn-primary{background:linear-gradient(120deg,var(--primary),var(--p-dark));color:#fff;
  box-shadow:0 14px 28px -12px rgba(124,58,237,.8)}
.al-dash .btn-primary:hover{transform:translateY(-2px);box-shadow:0 20px 34px -12px rgba(124,58,237,.9)}
.al-dash .btn-ghost{background:var(--glass-strong);border:1px solid var(--glass-brd);color:var(--p-800)}
.al-dash .btn-ghost:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm)}
.al-dash .btn svg{width:18px;height:18px}

/* form */
.al-dash .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:640px){.al-dash .form-grid{grid-template-columns:1fr}}
.al-dash .field{display:flex;flex-direction:column;gap:7px}
.al-dash .field.full{grid-column:1/-1}
.al-dash .field label{font-size:13px;font-weight:600;color:var(--p-800)}
.al-dash .field input,.al-dash .field select,.al-dash .field textarea{
  font-family:inherit;font-size:14px;color:var(--text);padding:13px 15px;border-radius:13px;
  border:1px solid rgba(124,58,237,.2);background:var(--glass-strong);outline:none;transition:.2s;width:100%}
.al-dash .field textarea{resize:vertical;min-height:110px}
.al-dash .field input:focus,.al-dash .field select:focus,.al-dash .field textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(124,58,237,.15)}

/* metier chips (nouvelle demande) */
.al-dash .chips{display:flex;flex-wrap:wrap;gap:10px}
.al-dash .chip{display:flex;align-items:center;gap:8px;padding:10px 15px;border-radius:13px;cursor:pointer;
  background:var(--glass-strong);border:1px solid var(--glass-brd);font-size:13.5px;font-weight:600;color:var(--p-800);transition:.2s}
.al-dash .chip:hover{transform:translateY(-2px)}
.al-dash .chip.on{background:linear-gradient(120deg,var(--primary),var(--p-dark));color:#fff;border-color:transparent}

/* calendar */
.al-dash .cal{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
.al-dash .cal .dow{text-align:center;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;padding-bottom:6px}
.al-dash .cal .cell{aspect-ratio:1;border-radius:12px;display:flex;flex-direction:column;padding:8px;background:var(--glass-strong);
  border:1px solid rgba(124,58,237,.08);font-size:13px;font-weight:600;position:relative;transition:.2s}
.al-dash .cal .cell:hover{transform:scale(1.04);z-index:2;box-shadow:var(--shadow-sm)}
.al-dash .cal .cell.out{opacity:.35}
.al-dash .cal .cell.today{background:linear-gradient(135deg,var(--primary),var(--p-dark));color:#fff;box-shadow:0 12px 24px -12px rgba(124,58,237,.8)}
.al-dash .cal .cell .ev{margin-top:auto;font-size:10px;font-weight:700;padding:2px 6px;border-radius:6px;background:rgba(217,119,6,.18);color:#b45309}
.al-dash .cal .cell.today .ev{background:rgba(255,255,255,.28);color:#fff}

/* docs grid */
.al-dash .doc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px}
.al-dash .doc{padding:18px;border-radius:16px;background:var(--glass-strong);border:1px solid var(--glass-brd);transition:.25s;cursor:pointer}
.al-dash .doc:hover{transform:translateY(-5px);box-shadow:var(--shadow-sm)}
.al-dash .doc .d-ic{width:46px;height:46px;border-radius:12px;display:grid;place-items:center;color:#fff;margin-bottom:14px;
  background:linear-gradient(135deg,var(--primary),var(--p-dark))}
.al-dash .doc.img .d-ic{background:linear-gradient(135deg,var(--sec-light),var(--secondary))}
.al-dash .doc b{font-size:13.5px;display:block;word-break:break-word;line-height:1.3}
.al-dash .doc small{font-size:11.5px;color:var(--muted)}

/* profil */
.al-dash .profile-head{display:flex;align-items:center;gap:20px;padding:26px;flex-wrap:wrap}
.al-dash .avatar-circle-lg{width:88px;height:88px;border-radius:22px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,var(--primary),var(--p-dark));color:#fff;font-family:'Sora';font-weight:700;font-size:34px;
  border:3px solid #fff;box-shadow:var(--shadow-sm)}
.al-dash .profile-head h2{font-size:22px}
.al-dash .profile-head .pm{color:var(--muted);font-size:14px;margin-top:3px}
.al-dash .profile-head .pt{margin-top:8px;display:inline-flex;gap:7px;align-items:center;font-size:12.5px;color:var(--primary);font-weight:600}

/* stars */
.al-dash .stars{display:flex;gap:2px;color:var(--sec-light)}
.al-dash .stars svg{width:16px;height:16px;fill:currentColor;stroke:currentColor}
.al-dash .stars .off{color:rgba(107,91,149,.3);fill:transparent}

/* review card */
.al-dash .review{padding:18px;border-radius:16px;background:var(--glass-strong);border:1px solid var(--glass-brd);margin-bottom:14px}
.al-dash .review .r-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:9px}
.al-dash .review b{font-size:14.5px}
.al-dash .review p{font-size:13.5px;color:var(--muted);line-height:1.55}

/* support */
.al-dash .support-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
.al-dash .sup{padding:22px;text-align:center}
.al-dash .sup .s-ic{width:54px;height:54px;border-radius:16px;margin:0 auto 14px;display:grid;place-items:center;color:#fff;
  background:linear-gradient(135deg,var(--primary),var(--p-dark))}
.al-dash .sup h4{font-size:15px;margin-bottom:5px}
.al-dash .sup p{font-size:13px;color:var(--muted);line-height:1.5}

.al-dash .mb-18{margin-bottom:18px}
.al-dash .muted{color:var(--muted)}

/* overlay for mobile sidebar */
.al-dash .overlay{position:fixed;inset:0;background:rgba(46,16,101,.45);backdrop-filter:blur(3px);z-index:40;opacity:0;visibility:hidden;transition:.3s}
.al-dash .overlay.show{opacity:1;visibility:visible}

@media(max-width:900px){
  .al-dash .sidebar{position:fixed;left:0;top:0;z-index:50;transform:translateX(-105%);transition:.32s cubic-bezier(.4,0,.2,1);box-shadow:0 0 60px rgba(0,0,0,.4)}
  .al-dash .sidebar.open{transform:none}
  .al-dash .burger{display:grid}
}
</style>

<div class="orb o1"></div>
<div class="orb o2"></div>

<div class="shell">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="al-sidebar">
    <div class="brand">
      <img class="logo" src="<?= e(setting('logo','images/logo.jpeg')) ?>" alt="<?= e($SITE) ?>"
           onerror="this.style.background='linear-gradient(135deg,#7C3AED,#F59E0B)';this.removeAttribute('src')">
      <div>
        <b><?= e($SITE) ?></b>
        <span>Espace client</span>
      </div>
    </div>

    <nav class="nav">
      <?php foreach ($menu as $m): ?>
        <?php if ($m['id'] === 'nouvelle'): ?>
          <a href="{{ route('particulier.demande.create') }}" class="nav-item">
            <?= icon($m['icon']) ?>
            <span><?= e($m['label']) ?></span>
          </a>
        <?php else: ?>
          <div class="nav-item<?= $m['id']==='dashboard'?' active':'' ?>" data-page="<?= e($m['id']) ?>">
            <?= icon($m['icon']) ?>
            <span><?= e($m['label']) ?></span>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <div class="nav-sep"></div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="logout" style="width:100%;text-align:left;border-style:solid" onclick="return confirm('Se déconnecter de votre espace ?')">
        <?= icon('logout') ?><span>Déconnexion</span>
      </button>
    </form>
  </aside>

  <div class="overlay" id="al-overlay"></div>

  <!-- ============ MAIN ============ -->
  <div class="main">
    <header class="topbar">
      <button class="burger" id="al-burger" aria-label="Menu"><?= icon('menu') ?></button>
      <div class="page-title">
        <h1 id="al-pageTitle">Tableau de bord</h1>
        <p id="al-pageSub">Bonjour <?= e(explode(' ', $user['name'])[0]) ?>, voici l'activité de votre espace.</p>
      </div>
      <label class="search">
        <?= icon('search') ?>
        <input type="text" placeholder="Rechercher une mission, un devis, un artisan…">
      </label>
      <button class="icon-btn" data-goto="notifications" aria-label="Notifications"><?= icon('bell') ?><span class="dot"></span></button>
      <button class="icon-btn" data-goto="messages" aria-label="Messages"><?= icon('chat') ?><span class="dot"></span></button>
      <button class="avatar-btn" data-goto="profil">
        <span class="avatar-circle"><?= e($user['initial']) ?></span>
        <span class="who">
          <span class="nm"><?= e($user['name']) ?></span>
          <span class="rl">Particulier</span>
        </span>
      </button>
    </header>

    <div class="content">

      <!-- ========== TABLEAU DE BORD ========== -->
      <section class="page show" id="page-dashboard">
        <div class="kpi-grid">
          <?php foreach ($stats as $s): ?>
          <div class="card kpi reveal">
            <div class="k-ic"><?= icon($s['icon']) ?></div>
            <?php if($s['delta']): ?><span class="k-delta <?= $s['up']?'up':'down' ?>"><?= e($s['delta']) ?></span><?php endif; ?>
            <div class="k-lab"><?= e($s['label']) ?></div>
            <div class="k-val" data-count="<?= e(preg_replace('/\s/','',$s['value'])) ?>"><?= e($s['value']) ?></div>
            <div class="k-unit"><?= e($s['unit']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="grid-2">
          <div>
            <div class="card chart-card reveal mb-18">
              <div class="sec-head">
                <h2>Aperçu des dépenses</h2>
                <span class="muted" style="font-size:13px;font-weight:600">7 derniers mois • FCFA (k)</span>
              </div>
              <div class="bars" id="al-bars">
                <?php foreach ($chart as $i => $v): $h = round($v / $chartMax * 100); ?>
                <div class="bar-wrap">
                  <div class="bar <?= $i===count($chart)-1?'hl':'' ?>" data-h="<?= $h ?>" title="<?= $v ?>k"></div>
                  <span><?= e($chartLabels[$i]) ?></span>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="card reveal" style="padding:20px">
              <div class="sec-head">
                <h2>Missions en cours</h2>
                <span class="link" data-page="missions">Tout voir <?= icon('arrow') ?></span>
              </div>
              <?php if(empty($missions)): ?>
                <div class="empty">Vous n'avez pas encore de mission. <a href="{{ route('particulier.demande.create') }}" class="link" style="display:inline-flex">Créer une demande <?= icon('arrow') ?></a></div>
              <?php else: ?>
                <?php foreach (array_slice($missions,0,3) as $m): ?>
                <div class="mission">
                  <img class="thumb" src="<?= e(setting($m['img'])) ?>" alt="" onerror="this.style.background='#A78BFA';this.removeAttribute('src')">
                  <div class="m-body">
                    <div class="m-top">
                      <h4><?= e($m['titre']) ?></h4>
                      <span class="badge <?= statutClass($m['statut']) ?>"><?= e($m['statut_label']) ?></span>
                    </div>
                    <div class="m-sub"><?= e($m['artisan']) ?> • <?= e($m['metier']) ?></div>
                    <div class="progress"><i data-w="<?= (int)$m['avance'] ?>"></i></div>
                    <div class="m-foot"><span><?= (int)$m['avance'] ?>% terminé</span><span><?= e($m['date']) ?></span></div>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <div>
            <div class="card reveal mb-18" style="padding:20px">
              <div class="sec-head"><h2>Notifications</h2><span class="link" data-page="notifications">Voir tout <?= icon('arrow') ?></span></div>
              <?php if(empty($notifications)): ?>
                <div class="empty">Aucune notification pour le moment.</div>
              <?php else: ?>
                <?php foreach (array_slice($notifications,0,4) as $n): ?>
                <div class="li n-<?= e($n['type']) ?>">
                  <div class="li-ic"><?= icon('bell') ?></div>
                  <div class="li-main"><b style="font-weight:500;font-size:13.5px"><?= e($n['text']) ?></b><small><?= e($n['time']) ?></small></div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <div class="card reveal" style="padding:20px">
              <div class="sec-head"><h2>Devis en attente</h2><span class="link" data-page="devis">Gérer <?= icon('arrow') ?></span></div>
              <?php $devisAttente = array_filter($devis, fn($d)=>$d['statut']==='devis_en_attente_validation'); ?>
              <?php if(empty($devisAttente)): ?>
                <div class="empty">Aucun devis en attente.</div>
              <?php else: ?>
                <?php foreach ($devisAttente as $d): ?>
                <div class="li">
                  <div class="li-ic"><?= icon('quote') ?></div>
                  <div class="li-main"><b><?= e($d['objet']) ?></b><small><?= e($d['artisan']) ?></small></div>
                  <div style="text-align:right"><div class="amount"><?= e($d['montant']) ?></div><small class="muted" style="font-size:11px">FCFA</small></div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== NOUVELLE DEMANDE (renvoi vers la vraie page) ========== -->
      <section class="page" id="page-nouvelle">
        <div class="grid-2">
          <div class="card reveal" style="padding:26px;text-align:center">
            <div class="k-ic" style="margin:0 auto 18px"><?= icon('plus') ?></div>
            <h2 class="section-title" style="font-size:20px;margin-bottom:8px">Lancer une nouvelle demande</h2>
            <p class="muted mb-18" style="font-size:14px">Décrivez votre besoin, ajoutez des photos et votre localisation : un artisan qualifié sera affecté rapidement.</p>
            <a href="{{ route('particulier.demande.create') }}" class="btn btn-primary"><?= icon('arrow') ?>Créer ma demande</a>
          </div>
          <div class="card reveal" style="padding:22px">
            <div class="sec-head"><h2>Comment ça marche</h2></div>
            <?php
              $steps = [['1','Décrivez le besoin','Métier, description, photos et adresse.'],
                        ['2','Un artisan est affecté','Sous 20 minutes en moyenne.'],
                        ['3','Devis puis acompte','Vous validez avant tout démarrage.'],
                        ['4','Payez en sécurité','Mobile Money (Moov / Yas), Artilo gère la répartition.']];
              foreach ($steps as $st): ?>
              <div class="li">
                <div class="li-ic" style="font-family:'Sora';font-weight:700"><?= $st[0] ?></div>
                <div class="li-main"><b><?= e($st[1]) ?></b><small><?= e($st[2]) ?></small></div>
              </div>
            <?php endforeach; ?>
            <div style="margin-top:16px;padding:16px;border-radius:14px;background:linear-gradient(135deg,rgba(124,58,237,.1),rgba(245,158,11,.08));border:1px solid var(--glass-brd)">
              <b style="font-family:'Sora';font-size:14px">Besoin d'aide ?</b>
              <p class="muted" style="font-size:13px;margin-top:4px">Notre équipe est joignable au <?= e(setting('contact_phone')) ?>.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== MES MISSIONS ========== -->
      <section class="page" id="page-missions">
        <div class="card reveal" style="padding:20px">
          <div class="sec-head"><h2>Toutes mes missions</h2><a href="{{ route('particulier.demande.create') }}" class="btn btn-primary"><?= icon('plus') ?>Nouvelle</a></div>
          <?php if(empty($missions)): ?>
            <div class="empty">Vous n'avez encore publié aucune demande.</div>
          <?php else: ?>
            <?php foreach ($missions as $m): ?>
            <div class="mission">
              <img class="thumb" src="<?= e(setting($m['img'])) ?>" alt="" onerror="this.style.background='#A78BFA';this.removeAttribute('src')">
              <div class="m-body">
                <div class="m-top"><h4><?= e($m['titre']) ?></h4><span class="badge <?= statutClass($m['statut']) ?>"><?= e($m['statut_label']) ?></span></div>
                <div class="m-sub"><?= e($m['artisan']) ?> • <?= e($m['metier']) ?></div>
                <div class="progress"><i data-w="<?= (int)$m['avance'] ?>"></i></div>
                <div class="m-foot"><span><?= (int)$m['avance'] ?>% terminé</span><span><?= e($m['date']) ?></span></div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== MES DEVIS ========== -->
      <section class="page" id="page-devis">
        <div class="card tbl-card reveal">
          <div class="sec-head" style="padding:16px 16px 4px"><h2>Mes devis</h2></div>
          <?php if(empty($devis)): ?>
            <div class="empty" style="margin:0 16px 16px">Aucun devis pour le moment. Cette fonctionnalité arrive bientôt !</div>
          <?php else: ?>
          <div class="tbl-scroll">
          <table class="tbl">
            <thead><tr><th>Référence</th><th>Objet</th><th>Artisan</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
            <?php foreach ($devis as $d): ?>
              <tr>
                <td class="ref"><?= e($d['ref']) ?></td>
                <td><?= e($d['objet']) ?></td>
                <td class="muted"><?= e($d['artisan']) ?></td>
                <td class="amount"><?= e($d['montant']) ?> <small class="muted" style="font-weight:500">FCFA</small></td>
                <td><span class="badge <?= statutClass($d['statut']) ?>"><?= e($d['statut_label']) ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== PAIEMENTS ========== -->
      <section class="page" id="page-paiements">
        <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr)">
          <div class="card kpi reveal"><div class="k-ic"><?= icon('wallet') ?></div><div class="k-lab">Total réglé</div><div class="k-val"><?= e($stats[3]['value']) ?></div><div class="k-unit">FCFA</div></div>
          <div class="card kpi reveal"><div class="k-ic"><?= icon('arrow') ?></div><div class="k-lab">Missions actives</div><div class="k-val"><?= e($stats[1]['value']) ?></div><div class="k-unit">en cours</div></div>
          <div class="card kpi reveal"><div class="k-ic"><?= icon('quote') ?></div><div class="k-lab">Devis à valider</div><div class="k-val"><?= e($stats[2]['value']) ?></div><div class="k-unit">en attente</div></div>
        </div>
        <div class="card tbl-card reveal">
          <div class="sec-head" style="padding:16px 16px 4px"><h2>Historique des transactions</h2></div>
          <?php if(empty($paiements)): ?>
            <div class="empty" style="margin:0 16px 16px">Aucun paiement pour l'instant. Cette fonctionnalité arrive bientôt !</div>
          <?php else: ?>
          <div class="tbl-scroll">
          <table class="tbl">
            <thead><tr><th>Référence</th><th>Libellé</th><th>Mode</th><th>Date</th><th style="text-align:right">Montant</th></tr></thead>
            <tbody>
            <?php foreach ($paiements as $p): ?>
              <tr>
                <td class="ref"><?= e($p['ref']) ?></td>
                <td><?= e($p['label']) ?></td>
                <td><span class="badge st-gray"><?= e($p['mode']) ?></span></td>
                <td class="muted"><?= e($p['date']) ?></td>
                <td style="text-align:right" class="amount <?= $p['sens'] ?>"><?= $p['sens']==='in'?'+':'−' ?><?= e($p['montant']) ?> <small class="muted" style="font-weight:500">FCFA</small></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== CALENDRIER ========== -->
      <section class="page" id="page-calendrier">
        <div class="card reveal" style="padding:24px">
          <div class="sec-head"><h2><?= e(ucfirst(now()->translatedFormat('F Y'))) ?></h2></div>
          <div class="empty">Le calendrier de vos interventions programmées arrivera bientôt ici.</div>
        </div>
      </section>

      <!-- ========== DOCUMENTS ========== -->
      <section class="page" id="page-documents">
        <div class="card reveal" style="padding:22px">
          <div class="sec-head"><h2>Mes documents</h2></div>
          <?php if(empty($documents)): ?>
            <div class="empty">Vos devis et factures téléchargeables en PDF apparaîtront ici.</div>
          <?php else: ?>
          <div class="doc-grid">
            <?php foreach ($documents as $d): ?>
            <div class="doc <?= $d['type']==='img'?'img':'' ?>">
              <div class="d-ic"><?= icon($d['type']==='img'?'grid':'doc') ?></div>
              <b><?= e($d['nom']) ?></b>
              <small><?= e($d['taille']) ?> • <?= e($d['date']) ?></small>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== MESSAGES ========== -->
      <section class="page" id="page-messages">
        <div class="card reveal" style="padding:14px 8px">
          <div class="sec-head" style="padding:8px 12px"><h2>Messagerie</h2></div>
          <?php if(empty($messages)): ?>
            <div class="empty" style="margin:0 12px 12px">Vous pourrez échanger ici avec votre artisan une fois une mission acceptée.</div>
          <?php else: ?>
          <?php foreach ($messages as $m): ?>
          <div class="li">
            <div class="li-ic" style="background:linear-gradient(135deg,var(--primary),var(--p-dark));color:#fff"><?= icon('user') ?></div>
            <div class="li-main"><b><?= e($m['from']) ?></b><small><?= e($m['text']) ?></small></div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px"><span class="li-time"><?= e($m['time']) ?></span><?php if($m['unread']): ?><span class="unread"></span><?php endif; ?></div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== NOTIFICATIONS ========== -->
      <section class="page" id="page-notifications">
        <div class="card reveal" style="padding:14px 8px">
          <div class="sec-head" style="padding:8px 12px"><h2>Toutes les notifications</h2></div>
          <?php if(empty($notifications)): ?>
            <div class="empty" style="margin:0 12px 12px">Aucune notification pour le moment.</div>
          <?php else: ?>
          <?php foreach ($notifications as $n): ?>
          <div class="li n-<?= e($n['type']) ?>">
            <div class="li-ic"><?= icon('bell') ?></div>
            <div class="li-main"><b style="font-weight:500"><?= e($n['text']) ?></b><small><?= e($n['time']) ?></small></div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== HISTORIQUE ========== -->
      <section class="page" id="page-historique">
        <div class="card reveal" style="padding:22px">
          <div class="sec-head"><h2>Historique d'activité</h2></div>
          <?php if(empty($historique)): ?>
            <div class="empty">Aucune mission archivée pour le moment.</div>
          <?php else: ?>
          <div style="position:relative;padding-left:8px">
          <?php foreach ($historique as $i=>$h): ?>
            <div style="display:flex;gap:16px;padding-bottom:<?= $i===count($historique)-1?'0':'20px' ?>;position:relative">
              <div style="display:flex;flex-direction:column;align-items:center">
                <div style="width:14px;height:14px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--sec-light));box-shadow:0 0 0 4px rgba(124,58,237,.15);flex-shrink:0"></div>
                <?php if($i!==count($historique)-1): ?><div style="width:2px;flex:1;background:rgba(124,58,237,.15);margin-top:4px"></div><?php endif; ?>
              </div>
              <div><b style="font-family:'Sora';font-size:15px"><?= e($h['action']) ?></b><div class="muted" style="font-size:13.5px;margin:2px 0 4px"><?= e($h['detail']) ?></div><small class="muted" style="font-size:12px"><?= e($h['date']) ?></small></div>
            </div>
          <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ========== AVIS ========== -->
      <section class="page" id="page-avis">
        <div class="grid-2">
          <div class="card reveal" style="padding:22px">
            <div class="sec-head"><h2>Mes avis publiés</h2></div>
            <?php if(empty($avis)): ?>
              <div class="empty">Vous n'avez pas encore laissé d'avis.</div>
            <?php else: ?>
              <?php foreach ($avis as $a): ?>
              <div class="review">
                <div class="r-top">
                  <b><?= e($a['artisan']) ?></b>
                  <div class="stars"><?php for($i=1;$i<=5;$i++): ?><svg class="<?= $i<=$a['note']?'':'off' ?>" viewBox="0 0 24 24"><polygon points="12 2 15.1 8.6 22 9.3 17 14 18.2 21 12 17.6 5.8 21 7 14 2 9.3 8.9 8.6 12 2"/></svg><?php endfor; ?></div>
                </div>
                <p><?= e($a['text']) ?></p>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div class="card reveal" style="padding:22px">
            <div class="sec-head"><h2>Laisser un avis</h2></div>
            <?php $artisansAvis = $missionsDb->pluck('artisan.user.name')->filter()->unique()->values(); ?>
            <?php if($artisansAvis->isEmpty()): ?>
              <div class="empty">Un avis pourra être laissé une fois qu'un artisan aura réalisé une mission pour vous.</div>
            <?php else: ?>
            <form onsubmit="return false" style="display:flex;flex-direction:column;gap:14px">
              <div class="field"><label>Artisan</label>
                <select>
                  <?php foreach ($artisansAvis as $nomArtisan): ?>
                    <option><?= e($nomArtisan) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Note</label>
                <div class="stars" id="al-rate" style="gap:6px;cursor:pointer">
                  <?php for($i=1;$i<=5;$i++): ?><svg data-v="<?= $i ?>" class="off" viewBox="0 0 24 24"><polygon points="12 2 15.1 8.6 22 9.3 17 14 18.2 21 12 17.6 5.8 21 7 14 2 9.3 8.9 8.6 12 2"/></svg><?php endfor; ?>
                </div>
              </div>
              <div class="field"><label>Commentaire</label><textarea placeholder="Partagez votre expérience…"></textarea></div>
              <button class="btn btn-primary" style="align-self:flex-start"><?= icon('star') ?>Publier l'avis</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- ========== SUPPORT ========== -->
      <section class="page" id="page-support">
        <div class="support-grid">
          <div class="card sup reveal"><div class="s-ic"><?= icon('phone') ?></div><h4>Téléphone</h4><p><?= e(setting('contact_phone')) ?></p></div>
          <div class="card sup reveal"><div class="s-ic"><?= icon('mail') ?></div><h4>Email</h4><p><?= e(setting('contact_email')) ?></p></div>
          <div class="card sup reveal"><div class="s-ic"><?= icon('pin') ?></div><h4>Adresse</h4><p><?= e(setting('contact_city')) ?></p></div>
        </div>
        <div class="card reveal" style="padding:26px">
          <div class="sec-head"><h2>Envoyer un message au support</h2></div>
          <form class="form-grid" onsubmit="return false">
            <div class="field"><label>Sujet</label><input type="text" placeholder="Objet de votre demande"></div>
            <div class="field"><label>Priorité</label><select><option>Normale</option><option>Haute</option></select></div>
            <div class="field full"><label>Votre message</label><textarea placeholder="Décrivez votre problème…"></textarea></div>
            <div class="field full"><button class="btn btn-primary" style="align-self:flex-start"><?= icon('chat') ?>Envoyer</button></div>
          </form>
        </div>
      </section>

      <!-- ========== MON PROFIL ========== -->
      <section class="page" id="page-profil">
        <div class="card reveal profile-head mb-18">
          <span class="avatar-circle-lg"><?= e($user['initial']) ?></span>
          <div>
            <h2><?= e($user['name']) ?></h2>
            <div class="pm"><?= e($user['email']) ?></div>
            <div class="pt"><?= icon('pin') ?><?= e($user['city']) ?> • <?= e($user['since']) ?></div>
          </div>
          <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
            @csrf
            <button type="submit" class="btn btn-ghost" onclick="return confirm('Se déconnecter ?')"><?= icon('logout') ?>Déconnexion</button>
          </form>
        </div>
        <div class="card reveal" style="padding:26px">
          <div class="sec-head"><h2>Informations personnelles</h2></div>
          <form class="form-grid" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="field"><label>Nom complet</label><input type="text" name="name" value="<?= e($user['name']) ?>"></div>
            <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>"></div>
            <div class="field"><label>Téléphone</label><input type="tel" name="phone" placeholder="+228 ..."></div>
            <div class="field"><label>Ville</label><input type="text" name="city" value="<?= e($user['city']) ?>"></div>
            <div class="field full"><label>Adresse</label><input type="text" name="address" placeholder="Quartier, rue…"></div>
            <div class="field full" style="flex-direction:row;gap:12px">
              <button type="submit" class="btn btn-primary"><?= icon('user') ?>Enregistrer</button>
            </div>
          </form>
        </div>
      </section>

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /shell -->
</div><!-- /al-dash -->

<script>
(function(){
  const scope = document.querySelector('.al-dash');
  if(!scope) return;

  const titles = {
    dashboard:['Tableau de bord','Bonjour <?= e(explode(' ',$user['name'])[0]) ?>, voici l\'activité de votre espace.'],
    nouvelle:['Nouvelle demande','Publiez un projet et recevez des devis d\'artisans vérifiés.'],
    missions:['Mes missions','Suivez l\'avancement de tous vos travaux.'],
    devis:['Mes devis','Comparez et validez les propositions reçues.'],
    paiements:['Paiements','Historique et gestion de vos transactions.'],
    calendrier:['Calendrier','Vos rendez-vous et interventions planifiés.'],
    documents:['Documents','Contrats, devis et factures au même endroit.'],
    messages:['Messages','Échangez avec vos artisans et le support.'],
    notifications:['Notifications','Toutes vos alertes récentes.'],
    historique:['Historique','Le journal complet de votre activité.'],
    avis:['Avis','Notez les artisans et consultez vos évaluations.'],
    support:['Support','Notre équipe est là pour vous aider.'],
    profil:['Mon profil','Gérez vos informations personnelles.']
  };

  const items = scope.querySelectorAll('.nav-item');
  const pages = scope.querySelectorAll('.page');
  const sidebar = scope.querySelector('#al-sidebar');
  const overlay = scope.querySelector('#al-overlay');

  function go(id){
    pages.forEach(p=>p.classList.remove('show'));
    const el = scope.querySelector('#page-'+id);
    if(!el) return;
    el.classList.add('show');
    items.forEach(i=>i.classList.toggle('active', i.dataset.page===id));
    const t = titles[id];
    if(t){ scope.querySelector('#al-pageTitle').textContent=t[0]; scope.querySelector('#al-pageSub').innerHTML=t[1]; }
    animate(el);
    closeSidebar();
    scope.scrollIntoView({block:'start'});
  }

  scope.querySelectorAll('[data-page]').forEach(n=>n.addEventListener('click',()=>go(n.dataset.page)));
  scope.querySelectorAll('[data-goto]').forEach(n=>n.addEventListener('click',()=>go(n.dataset.goto)));

  function openSidebar(){sidebar.classList.add('open');overlay.classList.add('show');}
  function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('show');}
  scope.querySelector('#al-burger')?.addEventListener('click',openSidebar);
  overlay?.addEventListener('click',closeSidebar);

  function animate(container){
    const root = container || scope;
    root.querySelectorAll('.reveal').forEach((el,i)=>{
      setTimeout(()=>el.classList.add('in'), i*70);
    });
    setTimeout(()=>{
      root.querySelectorAll('.bar').forEach(b=>b.style.height=b.dataset.h+'%');
      root.querySelectorAll('.progress i').forEach(p=>p.style.width=p.dataset.w+'%');
      root.querySelectorAll('.k-val[data-count]').forEach(countUp);
    },120);
  }
  function countUp(el){
    if(el.dataset.done) return; el.dataset.done=1;
    const raw = el.dataset.count; const num = parseInt(raw,10);
    if(isNaN(num)) return;
    const dur=1000, t0=performance.now();
    function step(t){
      const p=Math.min((t-t0)/dur,1);
      const val=Math.floor(num*(1-Math.pow(1-p,3)));
      el.textContent = val.toLocaleString('fr-FR');
      if(p<1) requestAnimationFrame(step); else el.textContent = num.toLocaleString('fr-FR');
    }
    requestAnimationFrame(step);
  }

  const rate=scope.querySelector('#al-rate');
  if(rate){
    const stars=[...rate.querySelectorAll('svg')];
    stars.forEach(s=>s.addEventListener('click',()=>{
      const v=+s.dataset.v; stars.forEach(x=>x.classList.toggle('off',+x.dataset.v>v));
    }));
  }

  animate(scope.querySelector('#page-dashboard'));
})();
</script>
</x-app-layout>