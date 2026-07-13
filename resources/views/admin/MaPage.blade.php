{{--
============================================================
  ESPACE ADMINISTRATEUR - ARTILO
  Refonte complete : dashboard pro, sidebar coulissable/masquable,
  barre de recherche globale, graphiques (Chart.js), modules complets.
  Les variables ($settings, $totalArtisans, $customerCount, $pendingCount,
  $latestCandidates, $incompleteProfilesCount, $profileCount, $approvedCount,
  $rejectedCount ...) et les routes restent celles de votre projet.
============================================================
--}}
@php
    // ---------------------------------------------------------
    // 1. PARAMETRES DU SITE (valeurs reelles de votre projet)
    // ---------------------------------------------------------
    $siteName    = $settings['site_name'] ?? 'Artilo';
    $logo        = $settings['logo'] ?? null;

    $primary     = $settings['color_primary'] ?? '#7C3AED';
    $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';
    $primaryText = $settings['color_primary_900'] ?? '#2E1065';
    $secondary   = $settings['color_secondary'] ?? '#D97706';
    $bg          = $settings['color_bg_2'] ?? '#FAF8FF';
    $muted       = $settings['color_muted'] ?? '#6B5B95';

    $adminUser  = Auth::user();
    $adminPhoto = $adminUser?->profile_photo ? asset('storage/' . $adminUser->profile_photo) : null;

    // ---------------------------------------------------------
    // 2. INDICATEURS CLES (valeurs reelles + placeholders projet)
    // ---------------------------------------------------------
    $stats = [
        ['id' => 'artisans',    'label' => 'Prestataires', 'value' => $totalArtisans, 'hint' => 'tous statuts',        'icon' => 'briefcase', 'trend' => '+8%'],
        ['id' => 'clients',     'label' => 'Particuliers',  'value' => $customerCount, 'hint' => 'comptes clients',     'icon' => 'users',     'trend' => '+12%'],
        ['id' => 'pending',     'label' => 'Candidatures',  'value' => $pendingCount,  'hint' => 'en attente',          'icon' => 'shield',    'trend' => 'a traiter'],
        ['id' => 'missions',    'label' => 'Missions/jour', 'value' => 0,              'hint' => 'module a connecter',  'icon' => 'clipboard', 'trend' => '--'],
        ['id' => 'revenus',     'label' => 'Revenus',       'value' => '0 F',          'hint' => 'paiements',           'icon' => 'wallet',    'trend' => '--'],
        ['id' => 'commissions', 'label' => 'Commissions',   'value' => '0 F',          'hint' => 'finance',             'icon' => 'coins',     'trend' => '--'],
    ];

    // ---------------------------------------------------------
    // 3. MENU LATERAL - tous les modules demandes
    // ---------------------------------------------------------
    $menuGroups = [
        [
            'id' => 'users', 'label' => 'Utilisateurs', 'icon' => 'users',
            'items' => [
                ['id' => 'clients',           'label' => 'Particuliers'],
                ['id' => 'artisans',          'label' => 'Prestataires'],
                ['id' => 'administrateurs',   'label' => 'Administrateurs'],
                ['id' => 'roles',             'label' => 'Roles & permissions'],
                ['id' => 'comptes-suspendus', 'label' => 'Comptes suspendus'],
            ],
        ],
        [
            'id' => 'validation', 'label' => 'Validation partenaires', 'icon' => 'shield',
            'items' => [
                ['id' => 'candidatures',      'label' => 'Candidatures'],
                ['id' => 'profils',           'label' => 'Profils partenaires'],
                ['id' => 'documents-identite','label' => 'Documents identite'],
                ['id' => 'contrats-signes',   'label' => 'Contrats signes'],
            ],
        ],
        [
            'id' => 'activity', 'label' => 'Activite', 'icon' => 'clipboard',
            'items' => [
                ['id' => 'demandes',     'label' => 'Demandes'],
                ['id' => 'attributions', 'label' => 'Attributions'],
                ['id' => 'affectations', 'label' => 'Affectations'],
                ['id' => 'prestations',  'label' => 'Prestations'],
                ['id' => 'metiers',      'label' => 'Catalogue metiers'],
                ['id' => 'zones',        'label' => 'Zones intervention'],
            ],
        ],
        [
            'id' => 'finance', 'label' => 'Finance', 'icon' => 'card',
            'items' => [
                ['id' => 'devis',         'label' => 'Devis'],
                ['id' => 'paiements',     'label' => 'Paiements'],
                ['id' => 'reversements',  'label' => 'Reversements'],
                ['id' => 'salaires',      'label' => 'Salaires journaliers'],
                ['id' => 'commissions',   'label' => 'Commissions'],
            ],
        ],
        [
            'id' => 'support', 'label' => 'Qualite & support', 'icon' => 'message',
            'items' => [
                ['id' => 'litiges',       'label' => 'Litiges'],
                ['id' => 'notifications', 'label' => 'Notifications'],
                ['id' => 'avis',          'label' => 'Avis'],
                ['id' => 'messages',      'label' => 'Messages'],
            ],
        ],
        [
            'id' => 'system', 'label' => 'Systeme', 'icon' => 'settings',
            'items' => [
                ['id' => 'statistiques', 'label' => 'Statistiques'],
                ['id' => 'documents',    'label' => 'Documents'],
                ['id' => 'journal',      'label' => 'Journal activite'],
                ['id' => 'parametres',   'label' => 'Parametres'],
            ],
        ],
    ];

    // Table ID -> Label (pour les sections generiques)
    $moduleLabels = [];
    foreach ($menuGroups as $group) {
        foreach ($group['items'] as $item) {
            $moduleLabels[$item['id']] = $item['label'];
        }
    }

    // Sections rendues specifiquement (les autres = template generique)
    $customSections = ['candidatures', 'profils', 'attributions', 'metiers'];

    // ---------------------------------------------------------
    // 4. ICONES SVG (Lucide)
    // ---------------------------------------------------------
    $svgIcon = function ($name, $class = 'admin-icon') {
        $icons = [
            'home'      => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
            'users'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
            'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 11h8"/><path d="M8 16h6"/>',
            'card'      => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
            'message'   => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/><path d="M8 9h8"/><path d="M8 13h5"/>',
            'settings'  => '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.08a1.8 1.8 0 0 0-1-.52 1.8 1.8 0 0 0-1.98.36l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1H4a2 2 0 1 1 0-4h.08a1.8 1.8 0 0 0 .52-1 1.8 1.8 0 0 0-.36-1.98l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6V4a2 2 0 1 1 4 0v.08a1.8 1.8 0 0 0 1 .52 1.8 1.8 0 0 0 1.98-.36l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05A1.8 1.8 0 0 0 19.4 9c.22.31.42.65.6 1H20a2 2 0 1 1 0 4h-.08a1.8 1.8 0 0 0-.52 1Z"/>',
            'wallet'    => '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0 0 4h15a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5"/><path d="M16 12h.01"/>',
            'coins'     => '<circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/>',
            'chart'     => '<path d="M3 3v18h18"/><path d="m7 15 3-4 3 3 4-6"/>',
            'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
            'search'    => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'menu'      => '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>',
            'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
            'check'     => '<path d="M20 6 9 17l-5-5"/>',
            'plus'      => '<path d="M12 5v14"/><path d="M5 12h14"/>',
            'trash'     => '<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        ];
        return '<svg class="' . $class . '" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$name] ?? $icons['home']) . '</svg>';
    };
@endphp

<x-app-layout>
    <div
        x-data="adminDashboard()"
        class="admin-page"
        :class="{ 'is-collapsed': sidebarCollapsed, 'is-open': sidebarOpen }"
        style="
            --admin-primary: {{ $primary }};
            --admin-primary-dark: {{ $primaryDark }};
            --admin-primary-text: {{ $primaryText }};
            --admin-secondary: {{ $secondary }};
            --admin-bg: {{ $bg }};
            --admin-muted: {{ $muted }};
        "
    >
        <style>
            [x-cloak] { display: none !important; }

            .admin-page {
                --sidebar-w: 264px;
                --sidebar-w-collapsed: 76px;
                min-height: 100vh;
                color: var(--admin-primary-text);
                background:
                    radial-gradient(1200px 600px at 88% -8%, color-mix(in srgb, var(--admin-secondary) 16%, transparent), transparent 60%),
                    radial-gradient(1000px 620px at -6% 4%, color-mix(in srgb, var(--admin-primary) 18%, transparent), transparent 60%),
                    linear-gradient(160deg, var(--admin-bg), #ffffff 46%, color-mix(in srgb, var(--admin-primary) 5%, #fff));
                position: relative;
            }

            .admin-shell {
                display: grid;
                grid-template-columns: var(--sidebar-w) minmax(0, 1fr);
                min-height: 100vh;
                transition: grid-template-columns .28s cubic-bezier(.4,0,.2,1);
            }
            .admin-page.is-collapsed .admin-shell { grid-template-columns: var(--sidebar-w-collapsed) minmax(0, 1fr); }

            .admin-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden;
                color: #fff;
                background:
                    radial-gradient(600px 260px at 20% -5%, color-mix(in srgb, var(--admin-primary) 55%, transparent), transparent 70%),
                    linear-gradient(185deg, #14101f 0%, #0d0a17 55%, #08060f 100%);
                border-right: 1px solid rgba(255,255,255,.07);
                box-shadow: 22px 0 60px -50px #000;
                z-index: 40;
                transition: transform .3s cubic-bezier(.4,0,.2,1);
            }
            .admin-sidebar::-webkit-scrollbar { width: .4rem; }
            .admin-sidebar::-webkit-scrollbar-thumb { border-radius: 999px; background: rgba(255,255,255,.18); }

            .admin-brand {
                display: flex; align-items: center; gap: .7rem;
                padding: 1.15rem 1.15rem;
                position: sticky; top: 0; z-index: 2;
                background: linear-gradient(180deg, rgba(20,16,31,.96), rgba(20,16,31,.6));
                backdrop-filter: blur(8px);
            }
            .admin-brand-logo {
                width: 42px; height: 42px; border-radius: 13px; flex: 0 0 auto;
                display: grid; place-items: center; overflow: hidden;
                background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
                box-shadow: 0 12px 26px -12px var(--admin-primary);
                font-weight: 900; color: #fff; font-size: 1.15rem;
            }
            .admin-brand-logo img { width: 100%; height: 100%; object-fit: cover; }
            .admin-brand-name { font-weight: 900; font-size: 1.15rem; letter-spacing: .3px; white-space: nowrap; }
            .admin-brand-sub { font-size: .68rem; color: rgba(255,255,255,.5); font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }

            .admin-nav { padding: .5rem .7rem 2rem; }
            .admin-group-label {
                font-size: .66rem; font-weight: 800; letter-spacing: 1.4px; text-transform: uppercase;
                color: rgba(255,255,255,.38); padding: 1rem .8rem .35rem;
            }
            .admin-nav-btn {
                display: flex; align-items: center; gap: .8rem; width: 100%;
                padding: .62rem .8rem; border-radius: .7rem;
                color: rgba(255,255,255,.72); font-weight: 700; font-size: .9rem;
                position: relative; overflow: hidden;
                transition: color .2s, background .2s, transform .18s;
            }
            .admin-nav-btn .admin-icon { width: 1.15rem; height: 1.15rem; flex: 0 0 auto; }
            .admin-nav-btn:hover { background: rgba(255,255,255,.07); color: #fff; transform: translateX(3px); }
            .admin-nav-btn .label { white-space: nowrap; transition: opacity .2s; }
            .admin-nav-btn.is-active {
                color: #fff;
                background: linear-gradient(135deg, color-mix(in srgb, var(--admin-primary) 92%, #000), color-mix(in srgb, var(--admin-secondary) 60%, var(--admin-primary-dark)));
                box-shadow: 0 16px 30px -18px var(--admin-primary);
            }
            .admin-nav-btn.is-active::before {
                content: ""; position: absolute; left: 0; top: 18%; height: 64%; width: 3px;
                border-radius: 999px; background: #fff;
            }
            .admin-badge {
                margin-left: auto; font-size: .68rem; font-weight: 900;
                padding: .1rem .45rem; border-radius: 999px;
                background: var(--admin-secondary); color: #fff;
            }

            .admin-page.is-collapsed .admin-brand-text,
            .admin-page.is-collapsed .admin-group-label,
            .admin-page.is-collapsed .admin-nav-btn .label,
            .admin-page.is-collapsed .admin-nav-btn .admin-badge { display: none; }
            .admin-page.is-collapsed .admin-nav-btn { justify-content: center; gap: 0; padding: .62rem; }
            .admin-page.is-collapsed .admin-brand { justify-content: center; }

            .admin-main { min-width: 0; display: flex; flex-direction: column; }
            .admin-topbar {
                position: sticky; top: 0; z-index: 30;
                display: flex; align-items: center; gap: 1rem;
                padding: .85rem 1.25rem;
                background: rgba(255,255,255,.72);
                backdrop-filter: blur(18px) saturate(160%);
                border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 12%, transparent);
            }
            .admin-icon-btn {
                width: 42px; height: 42px; border-radius: 12px; flex: 0 0 auto;
                display: grid; place-items: center; position: relative;
                border: 1px solid color-mix(in srgb, var(--admin-primary) 16%, transparent);
                background: #fff; color: var(--admin-primary-text);
                transition: transform .15s, box-shadow .2s, background .2s;
            }
            .admin-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -14px var(--admin-primary); background: color-mix(in srgb, var(--admin-primary) 6%, #fff); }
            .admin-icon-btn .admin-icon { width: 1.25rem; height: 1.25rem; }
            .admin-dot {
                position: absolute; top: 8px; right: 9px; width: 9px; height: 9px; border-radius: 999px;
                background: var(--admin-secondary); border: 2px solid #fff;
            }

            .admin-search {
                flex: 1; max-width: 520px; position: relative;
            }
            .admin-search .admin-icon {
                position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
                width: 1.15rem; height: 1.15rem; color: var(--admin-muted); pointer-events: none;
            }
            .admin-search input {
                width: 100%; padding: .72rem 1rem .72rem 2.7rem;
                border-radius: 12px; font-size: .92rem; font-weight: 600;
                border: 1px solid color-mix(in srgb, var(--admin-primary) 15%, transparent);
                background: color-mix(in srgb, var(--admin-primary) 4%, #fff);
                color: var(--admin-primary-text); outline: none;
                transition: border-color .2s, box-shadow .2s, background .2s;
            }
            .admin-search input::placeholder { color: color-mix(in srgb, var(--admin-muted) 80%, transparent); }
            .admin-search input:focus {
                border-color: var(--admin-primary); background: #fff;
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--admin-primary) 14%, transparent);
            }

            .admin-profile { display: flex; align-items: center; gap: .65rem; }
            .admin-avatar {
                width: 42px; height: 42px; border-radius: 12px; overflow: hidden; flex: 0 0 auto;
                display: grid; place-items: center; color: #fff; font-weight: 900;
                background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
                box-shadow: 0 12px 24px -14px var(--admin-primary);
            }
            .admin-avatar img { width: 100%; height: 100%; object-fit: cover; }

            .admin-content { padding: 1.5rem 1.25rem 3rem; }
            @media (min-width: 1024px) { .admin-content { padding: 1.75rem 2rem 3rem; } }

            .admin-card {
                border-radius: 20px;
                border: 1px solid color-mix(in srgb, var(--admin-primary) 12%, transparent);
                background: rgba(255,255,255,.9);
                box-shadow: 0 30px 60px -48px color-mix(in srgb, var(--admin-primary-dark) 80%, #000);
                backdrop-filter: blur(10px);
            }

            .admin-kpi {
                border-radius: 18px; padding: 1.15rem 1.2rem; color: #fff; position: relative; overflow: hidden;
                box-shadow: 0 26px 50px -30px color-mix(in srgb, var(--admin-primary) 70%, #000);
                animation: kpiIn .55s cubic-bezier(.2,.7,.3,1) backwards;
            }
            .admin-kpi::after {
                content: ""; position: absolute; inset: 0;
                background: radial-gradient(120px 120px at 88% 12%, rgba(255,255,255,.28), transparent 70%);
            }
            .admin-kpi .kpi-icon {
                width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
                background: rgba(255,255,255,.2); backdrop-filter: blur(4px);
            }
            .admin-kpi .kpi-icon .admin-icon { width: 1.35rem; height: 1.35rem; }
            .admin-kpi .kpi-value { font-size: 1.7rem; font-weight: 900; line-height: 1.1; }
            .admin-kpi .kpi-label { font-weight: 700; opacity: .92; }
            .admin-kpi .kpi-hint { font-size: .74rem; opacity: .78; }
            .admin-kpi .kpi-trend {
                font-size: .72rem; font-weight: 800; padding: .12rem .5rem; border-radius: 999px;
                background: rgba(255,255,255,.22);
            }
            .kpi-v1 { background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark)); }
            .kpi-v2 { background: linear-gradient(135deg, var(--admin-secondary), color-mix(in srgb, var(--admin-secondary) 55%, #b45309)); }
            .kpi-v3 { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
            .kpi-v4 { background: linear-gradient(135deg, #10b981, #059669); }
            .kpi-v5 { background: linear-gradient(135deg, #ec4899, #be185d); }
            .kpi-v6 { background: linear-gradient(135deg, #f59e0b, #d97706); }

            @keyframes kpiIn { from { opacity: 0; transform: translateY(18px) scale(.96); } to { opacity: 1; transform: none; } }

            .admin-title { font-size: 1.55rem; font-weight: 900; letter-spacing: -.5px; }
            .admin-subtitle { color: var(--admin-muted); font-weight: 600; }

            .admin-action {
                display: inline-flex; align-items: center; gap: .5rem;
                padding: .7rem 1.15rem; border-radius: 12px; font-weight: 800; font-size: .9rem;
                color: #fff; position: relative; overflow: hidden;
                background: linear-gradient(135deg, var(--admin-primary), color-mix(in srgb, var(--admin-secondary) 45%, var(--admin-primary-dark)));
                box-shadow: 0 18px 34px -20px var(--admin-primary); transition: transform .18s, box-shadow .2s;
                border: none; cursor: pointer;
            }
            .admin-action:hover { transform: translateY(-2px); box-shadow: 0 22px 40px -18px var(--admin-primary); }
            .admin-action.secondary {
                background: #fff; color: var(--admin-primary-text);
                border: 1px solid color-mix(in srgb, var(--admin-primary) 22%, transparent); box-shadow: none;
            }
            .admin-action.secondary:hover { background: color-mix(in srgb, var(--admin-primary) 6%, #fff); }
            .admin-action.danger {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                box-shadow: 0 18px 34px -20px #dc2626;
            }
            .admin-status {
                display: inline-flex; align-items: center; gap: .4rem;
                padding: .35rem .8rem; border-radius: 999px; font-size: .78rem; font-weight: 800;
                color: var(--admin-secondary);
                background: color-mix(in srgb, var(--admin-secondary) 14%, #fff);
                border: 1px solid color-mix(in srgb, var(--admin-secondary) 30%, transparent);
            }
            .admin-toast {
                position: fixed;
                top: 1.25rem;
                right: 1.25rem;
                z-index: 100;
                display: flex;
                align-items: flex-start;
                gap: .6rem;
                max-width: 380px;
                padding: .85rem 1rem;
                border-radius: 14px;
                font-weight: 700;
                font-size: .9rem;
                box-shadow: 0 20px 40px -18px rgba(0,0,0,.35);
                background: #fff;
            }
            .admin-toast.success { border-left: 4px solid #10b981; color: #047857; }
            .admin-toast.error { border-left: 4px solid #ef4444; color: #b91c1c; }
            .admin-toast .admin-toast-icon { width: 1.2rem; height: 1.2rem; flex: 0 0 auto; margin-top: .1rem; }
            .admin-toast .admin-toast-close {
                margin-left: auto; flex: 0 0 auto; color: inherit; opacity: .6;
                font-size: 1rem; line-height: 1; cursor: pointer; background: none; border: none;
            }
            .admin-toast .admin-toast-close:hover { opacity: 1; }
            @media (max-width: 640px) {
                .admin-toast { left: 1rem; right: 1rem; max-width: none; }
            }

            .admin-section { animation: sectionIn .45s cubic-bezier(.2,.7,.3,1); }
            @keyframes sectionIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

            .admin-table { width: 100%; border-collapse: collapse; }
            .admin-table th {
                text-align: left; font-size: .74rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
                color: var(--admin-muted); padding: .7rem 1rem;
                border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 12%, transparent);
            }
            .admin-table td { padding: .85rem 1rem; border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 8%, transparent); font-weight: 600; }
            .admin-table tr { transition: background .15s; }
            .admin-table tbody tr:hover { background: color-mix(in srgb, var(--admin-primary) 5%, transparent); }
            .admin-input-inline {
                width: 100%; padding: .45rem .6rem; border-radius: 8px; font-size: .88rem; font-weight: 600;
                border: 1px solid color-mix(in srgb, var(--admin-primary) 16%, transparent);
                background: color-mix(in srgb, var(--admin-primary) 3%, #fff);
                color: var(--admin-primary-text);
            }
            .admin-input-inline:focus {
                outline: none; border-color: var(--admin-primary);
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary) 14%, transparent);
            }

            .admin-empty {
                display: grid; place-items: center; gap: .5rem; padding: 2.5rem 1rem;
                color: var(--admin-muted); text-align: center;
            }
            .admin-empty .admin-icon { width: 2rem; height: 2rem; opacity: .6; }

            .admin-overlay {
                position: fixed; inset: 0; background: rgba(8,6,15,.55); backdrop-filter: blur(2px);
                z-index: 35; opacity: 0; pointer-events: none; transition: opacity .25s;
            }

            @media (max-width: 1023px) {
                .admin-shell { grid-template-columns: 1fr; }
                .admin-page.is-collapsed .admin-shell { grid-template-columns: 1fr; }
                .admin-sidebar {
                    position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
                    transform: translateX(-105%);
                }
                .admin-page.is-open .admin-sidebar { transform: translateX(0); }
                .admin-page.is-open .admin-overlay { opacity: 1; pointer-events: auto; }
                .admin-page.is-collapsed .admin-nav-btn .label,
                .admin-page.is-collapsed .admin-group-label,
                .admin-page.is-collapsed .admin-brand-text { display: block; }
                .admin-page.is-collapsed .admin-nav-btn { justify-content: flex-start; gap: .8rem; padding: .62rem .8rem; }
            }

            @media (prefers-reduced-motion: reduce) {
                * { animation: none !important; transition: none !important; }
            }
        </style>

        <div class="admin-overlay lg:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

        <div class="admin-shell">

            {{-- SIDEBAR --}}
            <aside class="admin-sidebar" aria-label="Navigation administrateur">
                <div class="admin-brand">
    <span class="admin-brand-logo">
        @if ($logo && file_exists(public_path($logo)))
            <img src="{{ asset($logo) }}" alt="{{ $siteName }}">
        @else
            {{ strtoupper(substr($siteName, 0, 1)) }}
        @endif
    </span>
    <span class="admin-brand-text">
        <span class="admin-brand-name">{{ $siteName }}</span><br>
        <span class="admin-brand-sub">Admin</span>
    </span>
</div>

                <nav class="admin-nav">
                    <button type="button" class="admin-nav-btn" :class="{ 'is-active': activeTab === 'dashboard' }" @click="go('dashboard')">
                        {!! $svgIcon('home') !!}
                        <span class="label">Tableau de bord</span>
                    </button>

                    @foreach ($menuGroups as $group)
                        <p class="admin-group-label">{{ $group['label'] }}</p>
                        @foreach ($group['items'] as $item)
                            <button
                                type="button"
                                class="admin-nav-btn"
                                :class="{ 'is-active': activeTab === '{{ $item['id'] }}' }"
                                @click="go('{{ $item['id'] }}')"
                            >
                                {!! $svgIcon($group['icon']) !!}
                                <span class="label">{{ $item['label'] }}</span>
                                @if ($item['id'] === 'candidatures')
                                    <span class="admin-badge">{{ $pendingCount }}</span>
                                @endif
                            </button>
                        @endforeach
                    @endforeach

                    <div class="my-3 border-t" style="border-color: rgba(255,255,255,.08);"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="admin-nav-btn w-full" style="color: rgba(255,255,255,.5);">
                            {!! $svgIcon('logout') !!}
                            <span class="label">Déconnexion</span>
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- MAIN --}}
            <div class="admin-main">

                {{-- TOPBAR --}}
                <header class="admin-topbar">
                    <button type="button" class="admin-icon-btn" @click="toggleSidebar()" aria-label="Afficher/masquer le menu">
                        {!! $svgIcon('menu') !!}
                    </button>

                    <div class="admin-search">
                        {!! $svgIcon('search') !!}
                        <input
                            type="search"
                            x-model="search"
                            placeholder="Rechercher un module, un client, un prestataire..."
                            aria-label="Recherche globale"
                        >
                    </div>

                    <button type="button" class="admin-icon-btn" aria-label="Notifications">
                        {!! $svgIcon('bell') !!}
                        <span class="admin-dot"></span>
                    </button>

                    <div class="admin-profile">
                        <span class="admin-avatar">
                            @if ($adminPhoto)
                                <img src="{{ $adminPhoto }}" alt="Photo administrateur">
                            @else
                                {{ strtoupper(substr($adminUser?->name ?? 'A', 0, 1)) }}
                            @endif
                        </span>
                        <div class="hidden sm:block leading-tight">
                            <p class="font-black text-sm">{{ $adminUser?->name ?? 'Administrateur' }}</p>
                            <p class="text-xs" style="color: var(--admin-muted)">Administrateur</p>
                        </div>
                    </div>
                </header>

                {{-- CONTENT --}}
                <main class="admin-content">

                    {{-- Toast de notification --}}
                    @if (session('success') || session('error'))
                        <div
                            x-data="{ show: true }"
                            x-show="show"
                            x-init="setTimeout(() => show = false, 4000)"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-3"
                            class="admin-toast {{ session('success') ? 'success' : 'error' }}"
                            x-cloak
                        >
                            @if (session('success'))
                                <svg class="admin-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            @else
                                <svg class="admin-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            @endif
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" class="admin-toast-close" @click="show = false" aria-label="Fermer">✕</button>
                        </div>
                    @endif

                    {{-- DASHBOARD --}}
                    <section x-show="activeTab === 'dashboard'" x-cloak class="admin-section space-y-6">
                        <div class="flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <h1 class="admin-title">Bonjour, {{ $adminUser?->name ?? 'Admin' }}</h1>
                                <p class="admin-subtitle mt-1">Vue d'ensemble de la plateforme {{ $siteName }}.</p>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.artisans.index') }}" class="admin-action">Voir les candidatures</a>
                                <a href="{{ route('admin.profils.index') }}" class="admin-action secondary">Profils partenaires</a>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($stats as $i => $stat)
                                <article class="admin-kpi kpi-v{{ ($i % 6) + 1 }}" style="animation-delay: {{ $i * 70 }}ms">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="kpi-icon">{!! $svgIcon($stat['icon']) !!}</span>
                                        <span class="kpi-trend">{{ $stat['trend'] }}</span>
                                    </div>
                                    <p class="kpi-value mt-3">{{ $stat['value'] }}</p>
                                    <p class="kpi-label">{{ $stat['label'] }}</p>
                                    <p class="kpi-hint">{{ $stat['hint'] }}</p>
                                </article>
                            @endforeach
                        </div>

                        <div class="grid gap-4 lg:grid-cols-3">
                            <article class="admin-card p-5 lg:col-span-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-black">Activite des missions</h2>
                                        <p class="text-sm" style="color: var(--admin-muted)">Evolution sur les 7 derniers mois</p>
                                    </div>
                                    <span class="admin-status">Temps reel</span>
                                </div>
                                <div class="mt-4" style="height: 280px"><canvas id="chartMissions"></canvas></div>
                            </article>

                            <article class="admin-card p-5">
                                <h2 class="text-lg font-black">Repartition des demandes</h2>
                                <p class="text-sm" style="color: var(--admin-muted)">Par statut</p>
                                <div class="mt-4" style="height: 280px"><canvas id="chartStatuts"></canvas></div>
                            </article>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-3">
                            <article class="admin-card p-5">
                                <h2 class="text-lg font-black">Metiers demandes</h2>
                                <p class="text-sm" style="color: var(--admin-muted)">Top interventions</p>
                                <div class="mt-4" style="height: 260px"><canvas id="chartMetiers"></canvas></div>
                            </article>

                            <article class="admin-card p-5 lg:col-span-2">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-lg font-black">Candidatures recentes</h2>
                                    <a href="{{ route('admin.artisans.index') }}" class="text-sm font-black" style="color: var(--admin-primary)">Tout voir</a>
                                </div>
                                <div class="mt-4 divide-y" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                                    @forelse ($latestCandidates as $artisan)
                                        <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                                            <div>
                                                <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                                                <p class="text-sm" style="color: var(--admin-muted)">{{ ucfirst($artisan->profession) }} - {{ $artisan->intervention_area }}</p>
                                            </div>
                                            <a href="{{ route('admin.artisans.index') }}" class="admin-status">Verifier</a>
                                        </div>
                                    @empty
                                        <p class="py-5 text-sm" style="color: var(--admin-muted)">Aucune candidature en attente pour le moment.</p>
                                    @endforelse
                                </div>
                            </article>
                        </div>
                    </section>

                    {{-- CANDIDATURES --}}
                    <section x-show="activeTab === 'candidatures'" x-cloak class="admin-section admin-card p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="admin-title">Candidatures - en attente</h2>
                                <p class="admin-subtitle mt-1">{{ $pendingCount }} candidature(s) a valider ou refuser.</p>
                            </div>
                            <a href="{{ route('admin.artisans.index') }}" class="admin-action">Voir les candidatures</a>
                        </div>
                        <div class="mt-5 overflow-hidden rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                            @forelse ($latestCandidates as $artisan)
                                <div class="grid gap-3 border-b bg-white p-4 md:grid-cols-[1fr_1fr_auto]" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                                    <div>
                                        <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                                        <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->user->email ?? 'Email non renseigne' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-bold">{{ ucfirst($artisan->profession) }}</p>
                                        <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->intervention_area }}</p>
                                    </div>
                                    <span class="admin-status self-center">En attente</span>
                                </div>
                            @empty
                                <p class="bg-white p-5 text-sm" style="color: var(--admin-muted)">Aucune candidature recente.</p>
                            @endforelse
                        </div>
                    </section>

                    {{-- PROFILS PARTENAIRES --}}
                    <section x-show="activeTab === 'profils'" x-cloak class="admin-section space-y-5">
                        <div class="admin-card p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="admin-title">Profils partenaires</h2>
                                    <p class="admin-subtitle mt-1">Etat des profils prestataires de la plateforme.</p>
                                </div>
                                <a href="{{ route('admin.profils.index') }}" class="admin-action">Voir les profils</a>
                            </div>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-xl p-4" style="background: color-mix(in srgb, var(--admin-primary) 7%, white)">
                                    <p class="font-black text-lg">{{ $incompleteProfilesCount }} profil(s) incomplet(s)</p>
                                    <p class="mt-1 text-sm" style="color: var(--admin-muted)">Documents, photos, adresse ou description a completer.</p>
                                </div>
                                <div class="rounded-xl p-4" style="background: color-mix(in srgb, var(--admin-secondary) 10%, white)">
                                    <p class="font-black text-lg">{{ $profileCount }} profil(s) enrichi(s)</p>
                                    <p class="mt-1 text-sm" style="color: var(--admin-muted)">Prets pour la vitrine publique apres verification.</p>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, #10b981 12%, white)">
                                    <p class="text-2xl font-black" style="color:#059669">{{ $approvedCount }}</p>
                                    <p class="text-sm" style="color: var(--admin-muted)">Valides</p>
                                </div>
                                <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, var(--admin-secondary) 12%, white)">
                                    <p class="text-2xl font-black" style="color: var(--admin-secondary)">{{ $pendingCount }}</p>
                                    <p class="text-sm" style="color: var(--admin-muted)">En attente</p>
                                </div>
                                <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, #ef4444 12%, white)">
                                    <p class="text-2xl font-black" style="color:#dc2626">{{ $rejectedCount }}</p>
                                    <p class="text-sm" style="color: var(--admin-muted)">Refuses</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ATTRIBUTIONS --}}
                    <section x-show="activeTab === 'attributions'" x-cloak class="admin-section space-y-5">
                        
                        <div class="admin-card p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="admin-title">Demandes en attente d'attribution</h2>
                                    <p class="admin-subtitle mt-1">
                                        <span class="font-black" style="color: var(--admin-secondary)">{{ $demandesEnAttente->total() ?? 0 }}</span> 
                                        demande(s) en attente d'un prestataire.
                                    </p>
                                </div>
                                <span class="admin-status" style="background: color-mix(in srgb, var(--admin-secondary) 16%, #fff);">
                                    ⚡ Action requise
                                </span>
                            </div>

                            @if(isset($demandesEnAttente) && $demandesEnAttente->isNotEmpty())
                                <div class="mt-5 overflow-x-auto rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Métier</th>
                                                <th>Description</th>
                                                <th>Date</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($demandesEnAttente as $demande)
                                                <tr>
                                                    <td class="font-black">#{{ $demande->id }}</td>
                                                    <td>
                                                        <p class="font-black">{{ $demande->particulier->name ?? 'Client' }}</p>
                                                        <p class="text-xs" style="color: var(--admin-muted)">{{ $demande->particulier->email ?? '' }}</p>
                                                    </td>
                                                    <td>
                                                        <span class="admin-status" style="background: color-mix(in srgb, var(--admin-primary) 12%, #fff); color: var(--admin-primary)">
                                                            {{ $demande->metier_requis ?? 'Non défini' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <p class="truncate max-w-[200px]">{{ $demande->description ?? 'Aucune description' }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="font-black">{{ $demande->created_at->format('d/m/Y') }}</p>
                                                        <p class="text-xs" style="color: var(--admin-muted)">{{ $demande->created_at->format('H:i') }}</p>
                                                    </td>
                                                    <td>
                                                        <div class="flex items-center justify-center gap-2">
                                                            <a href="{{ route('admin.attributions.show', $demande) }}" 
                                                               class="admin-action secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                                                👁️ Voir
                                                            </a>
                                                            <form action="{{ route('admin.attributions.attribuer', $demande) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="admin-action" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                                                    🔍 Attribuer
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if(isset($demandesEnAttente) && method_exists($demandesEnAttente, 'links'))
                                    <div class="mt-4">
                                        {{ $demandesEnAttente->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="admin-empty mt-5">
                                    {!! $svgIcon('check') !!}
                                    <p class="font-black">Aucune demande en attente</p>
                                    <p class="text-sm">Toutes les demandes ont déjà un prestataire attribué.</p>
                                </div>
                            @endif
                        </div>

                        <div class="admin-card p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="admin-title">Missions en attente d'acceptation</h2>
                                    <p class="admin-subtitle mt-1">
                                        <span class="font-black" style="color: var(--admin-secondary)">{{ $missionsAffectees->total() ?? 0 }}</span> 
                                        prestataire(s) ont été sollicités.
                                    </p>
                                </div>
                                <span class="admin-status" style="background: color-mix(in srgb, #0ea5e9 14%, #fff); color: #0ea5e9;">
                                    ⏳ En attente de réponse
                                </span>
                            </div>

                            @if(isset($missionsAffectees) && $missionsAffectees->isNotEmpty())
                                <div class="mt-5 overflow-x-auto rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Prestataire</th>
                                                <th>Métier</th>
                                                <th>Expire dans</th>
                                                <th style="text-align:center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($missionsAffectees as $mission)
                                                <tr>
                                                    <td class="font-black">#{{ $mission->id }}</td>
                                                    <td>
                                                        <p class="font-black">{{ $mission->particulier->name ?? 'Client' }}</p>
                                                        <p class="text-xs" style="color: var(--admin-muted)">{{ $mission->particulier->email ?? '' }}</p>
                                                    </td>
                                                    <td>
                                                        @if($mission->artisan)
                                                            <p class="font-black">{{ $mission->artisan->user->name ?? 'Prestataire' }}</p>
                                                            <p class="text-xs" style="color: var(--admin-muted)">Score: {{ $mission->artisan->score_interne ?? 'N/A' }}</p>
                                                        @else
                                                            <span class="text-sm" style="color: var(--admin-muted)">En attente</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="admin-status" style="background: color-mix(in srgb, var(--admin-primary) 12%, #fff); color: var(--admin-primary)">
                                                            {{ $mission->metier_requis ?? 'Non défini' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($mission->expire_le)
                                                            @php
                                                                $minutesRestantes = now()->diffInMinutes($mission->expire_le, false);
                                                                $isExpired = $minutesRestantes <= 0;
                                                            @endphp
                                                            <span class="admin-status" style="background: {{ $isExpired ? 'color-mix(in srgb, #ef4444 14%, #fff)' : 'color-mix(in srgb, var(--admin-secondary) 14%, #fff)' }}; color: {{ $isExpired ? '#dc2626' : 'var(--admin-secondary)' }}">
                                                                @if($isExpired)
                                                                    ⚠️ Expiré
                                                                @else
                                                                    ⏱️ {{ $minutesRestantes }} min
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="text-sm" style="color: var(--admin-muted)">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="flex items-center justify-center gap-2">
                                                            <a href="{{ route('admin.attributions.show', $mission) }}" 
                                                               class="admin-action secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                                                👁️ Voir
                                                            </a>
                                                            @if($mission->statut === 'affectee')
                                                                <form action="{{ route('admin.attributions.annuler', $mission) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="admin-action" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 18px 34px -20px #dc2626;" 
                                                                            onclick="return confirm('Annuler cette attribution ? La mission sera remise en attente.')">
                                                                        ❌ Annuler
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if(isset($missionsAffectees) && method_exists($missionsAffectees, 'links'))
                                    <div class="mt-4">
                                        {{ $missionsAffectees->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="admin-empty mt-5">
                                    {!! $svgIcon('check') !!}
                                    <p class="font-black">Aucune mission en attente</p>
                                    <p class="text-sm">Toutes les missions affectées ont reçu une réponse.</p>
                                </div>
                            @endif
                        </div>

                        <div class="admin-card p-5" style="background: color-mix(in srgb, var(--admin-primary) 4%, #fff); border-style: dashed;">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-black">💡 Astuce</h3>
                                    <p class="text-sm" style="color: var(--admin-muted)">
                                        L'attribution est automatique. Cliquez sur "Attribuer" pour lancer la recherche du meilleur prestataire disponible.
                                        Le prestataire a {{ config('artilo.delai_acceptation_minutes', 20) }} minutes pour accepter.
                                    </p>
                                </div>
                                <a href="{{ route('admin.attributions.index') }}" class="admin-action">
                                    📋 Voir toutes les attributions
                                </a>
                            </div>
                        </div>
                    </section>

                    {{-- CATALOGUE DES MÉTIERS (Phase 8) --}}
                    <section x-show="activeTab === 'metiers'" x-cloak class="admin-section space-y-5">

                        <div class="admin-card p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="admin-title">Catalogue des métiers</h2>
                                    <p class="admin-subtitle mt-1">
                                        <span class="font-black" style="color: var(--admin-primary)">{{ $metiers->count() }}</span>
                                        métier(s) référencé(s) au total.
                                    </p>
                                </div>
                                <span class="admin-status">
                                    {{ $metiers->where('actif', true)->count() }} actif(s)
                                </span>
                            </div>

                            <form method="POST" action="{{ route('admin.metiers.store') }}"
                                  class="mt-5 flex flex-col gap-3 sm:flex-row">
                                @csrf
                                <input
                                    type="text"
                                    name="nom"
                                    placeholder="Nom du nouveau métier (ex: Vitrerie)"
                                    class="admin-input-inline flex-1"
                                    required
                                >
                                <input
                                    type="text"
                                    name="description"
                                    placeholder="Description (optionnel)"
                                    class="admin-input-inline flex-1"
                                >
                                <button type="submit" class="admin-action">
                                    {!! $svgIcon('plus') !!}
                                    <span>Ajouter</span>
                                </button>
                            </form>
                            @error('nom')
                                <p class="mt-2 text-sm font-bold" style="color:#dc2626">{{ $message }}</p>
                            @enderror
                        </div>

                        @foreach ($metiers as $metier)
                            <form id="metier-update-{{ $metier->id }}" method="POST"
                                  action="{{ route('admin.metiers.update', $metier) }}" class="hidden">
                                @csrf
                                @method('PUT')
                            </form>
                            <form id="metier-delete-{{ $metier->id }}" method="POST"
                                  action="{{ route('admin.metiers.destroy', $metier) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach

                        <div class="admin-card p-5">
                            @if ($metiers->isNotEmpty())
                                <div class="overflow-x-auto rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Slug</th>
                                                <th>Prestataires</th>
                                                <th>Actif</th>
                                                <th style="text-align:center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($metiers as $metier)
                                                @php
                                                    $artisansCount = $metier->artisans()->count();
                                                    $hasArtisans = $artisansCount > 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            name="nom"
                                                            value="{{ $metier->nom }}"
                                                            form="metier-update-{{ $metier->id }}"
                                                            class="admin-input-inline"
                                                        >
                                                    </td>
                                                    <td class="text-sm" style="color: var(--admin-muted)">
                                                        {{ $metier->slug }}
                                                    </td>
                                                    <td>
                                                        <span class="admin-status" style="background: color-mix(in srgb, var(--admin-primary) 12%, #fff); color: var(--admin-primary)">
                                                            {{ $artisansCount }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="actif" value="0" form="metier-update-{{ $metier->id }}">
                                                        <input
                                                            type="checkbox"
                                                            name="actif"
                                                            value="1"
                                                            form="metier-update-{{ $metier->id }}"
                                                            {{ $metier->actif ? 'checked' : '' }}
                                                        >
                                                    </td>
                                                    <td>
                                                        <div class="flex items-center justify-center gap-2">
                                                            <button type="submit" form="metier-update-{{ $metier->id }}"
                                                                class="admin-action secondary" style="padding: .4rem .8rem; font-size: .78rem;">
                                                                {!! $svgIcon('check') !!}
                                                                <span>Enregistrer</span>
                                                            </button>
                                                            @if ($hasArtisans)
                                                                <span class="admin-status" style="background: color-mix(in srgb, #ef4444 10%, #fff); color: #dc2626; cursor: not-allowed;" title="{{ $artisansCount }} prestataire(s) rattaché(s) à ce métier">
                                                                    {!! $svgIcon('trash') !!}
                                                                    <span>Bloqué ({{ $artisansCount }})</span>
                                                                </span>
                                                            @else
                                                                <button type="submit" form="metier-delete-{{ $metier->id }}"
                                                                    class="admin-action danger" style="padding: .4rem .8rem; font-size: .78rem;"
                                                                    onclick="return confirm('Supprimer définitivement le métier "{{ $metier->nom }}" ? Cette action est irréversible.')">
                                                                    {!! $svgIcon('trash') !!}
                                                                    <span>Supprimer</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="admin-empty">
                                    {!! $svgIcon('clipboard') !!}
                                    <p class="font-black">Aucun métier pour l'instant</p>
                                    <p class="text-sm">Utilise le formulaire ci-dessus pour ajouter le premier métier du catalogue.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- MODULES GENERIQUES --}}
                    @foreach ($moduleLabels as $id => $label)
                        @if (! in_array($id, $customSections))
                            <section x-show="activeTab === '{{ $id }}'" x-cloak class="admin-section space-y-5">
                                <div class="admin-card p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h2 class="admin-title">{{ $label }}</h2>
                                            <p class="admin-subtitle mt-1">Gestion du module « {{ $label }} » de la plateforme.</p>
                                        </div>
                                        <button type="button" class="admin-action">
                                            {!! $svgIcon('search') !!}
                                            <span>Nouvelle entree</span>
                                        </button>
                                    </div>

                                    <div class="admin-search mt-5" style="max-width: 100%">
                                        {!! $svgIcon('search') !!}
                                        <input type="search" placeholder="Rechercher dans {{ strtolower($label) }}..." aria-label="Rechercher dans {{ $label }}">
                                    </div>

                                    <div class="mt-5 overflow-x-auto rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                                        <table class="admin-table">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Libelle</th>
                                                    <th>Statut</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="admin-empty">
                                                            {!! $svgIcon('clipboard') !!}
                                                            <p class="font-black">Module a connecter</p>
                                                            <p class="text-sm">Les donnees « {{ $label }} » s'afficheront ici une fois le controleur relie.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        @endif
                    @endforeach

                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            function adminDashboard() {
                return {
                    activeTab: 'dashboard',
                    sidebarCollapsed: false,
                    sidebarOpen: false,
                    search: '',
                    go(tab) {
                        this.activeTab = tab;
                        if (window.innerWidth < 1024) this.sidebarOpen = false;
                        this.$nextTick(() => { if (tab === 'dashboard') window.renderAdminCharts && window.renderAdminCharts(); });
                    },
                    toggleSidebar() {
                        if (window.innerWidth < 1024) { this.sidebarOpen = !this.sidebarOpen; }
                        else { this.sidebarCollapsed = !this.sidebarCollapsed; }
                    },
                };
            }

            (function () {
                function readVar(el, name, fallback) {
                    var v = getComputedStyle(el).getPropertyValue(name);
                    return (v && v.trim()) || fallback;
                }
                var charts = {};
                window.renderAdminCharts = function () {
                    if (typeof Chart === 'undefined') return;
                    var page = document.querySelector('.admin-page');
                    if (!page) return;
                    var primary   = readVar(page, '--admin-primary', '#7C3AED');
                    var secondary = readVar(page, '--admin-secondary', '#D97706');
                    var muted     = readVar(page, '--admin-muted', '#6B5B95');

                    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
                    Chart.defaults.color = muted;

                    var elLine = document.getElementById('chartMissions');
                    if (elLine && !charts.line) {
                        var ctx = elLine.getContext('2d');
                        var grad = ctx.createLinearGradient(0, 0, 0, 260);
                        grad.addColorStop(0, primary + '55');
                        grad.addColorStop(1, primary + '00');
                        charts.line = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil'],
                                datasets: [
                                    { label: 'Missions', data: [12, 19, 15, 27, 24, 33, 41], borderColor: primary, backgroundColor: grad, fill: true, tension: .4, borderWidth: 3, pointRadius: 3, pointBackgroundColor: primary },
                                    { label: 'Demandes', data: [18, 22, 20, 30, 29, 38, 47], borderColor: secondary, backgroundColor: 'transparent', fill: false, tension: .4, borderWidth: 3, pointRadius: 3, pointBackgroundColor: secondary, borderDash: [6, 5] }
                                ]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { labels: { usePointStyle: true, boxWidth: 8, font: { weight: '700' } } } },
                                scales: {
                                    x: { grid: { display: false } },
                                    y: { grid: { color: 'rgba(0,0,0,.06)' }, beginAtZero: true }
                                }
                            }
                        });
                    }

                    var elDo = document.getElementById('chartStatuts');
                    if (elDo && !charts.donut) {
                        charts.donut = new Chart(elDo.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: ['En attente', 'Affectees', 'En cours', 'Terminees'],
                                datasets: [{ data: [8, 12, 15, 34], backgroundColor: [secondary, '#0ea5e9', primary, '#10b981'], borderWidth: 0, hoverOffset: 8 }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false, cutout: '64%',
                                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14, font: { weight: '700' } } } }
                            }
                        });
                    }

                    var elBar = document.getElementById('chartMetiers');
                    if (elBar && !charts.bar) {
                        charts.bar = new Chart(elBar.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: ['Plomberie', 'Elec', 'Peinture', 'Maconnerie', 'Menuiserie'],
                                datasets: [{ label: 'Demandes', data: [24, 31, 18, 14, 9], backgroundColor: primary, borderRadius: 8, maxBarThickness: 34 }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { x: { grid: { display: false } }, y: { grid: { color: 'rgba(0,0,0,.06)' }, beginAtZero: true } }
                            }
                        });
                    }
                };

                if (document.readyState !== 'loading') { window.renderAdminCharts(); }
                else { document.addEventListener('DOMContentLoaded', window.renderAdminCharts); }
            })();
        </script>
    </div>
</x-app-layout>