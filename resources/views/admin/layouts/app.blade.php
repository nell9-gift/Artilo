{{--
====================================================================
   ARTILO — Layout Administrateur (Multi-pages)
   Sidebar violette · Topbar vitré · Fond dégradé
   Chaque module a sa propre page maintenant !
==================================================================== --}}
@php
use App\Models\Setting;
$settings = $settings ?? Setting::pluck('value', 'key')->toArray();
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
$initial    = strtoupper(substr($adminUser?->name ?? 'A', 0, 1));
$currentRoute = Route::currentRouteName();

// Modules — chaque module a sa page résumé + pages détail
$modules = [
    'dashboard'    => ['route' => 'admin.dashboard',           'label' => 'Tableau de bord',    'icon' => 'home',      'group' => null, 'prefixes' => ['admin.dashboard', 'admin.MaPage']],
    'particuliers' => ['route' => 'admin.particuliers-resume', 'label' => 'Particuliers',       'icon' => 'users',     'group' => 'Utilisateurs', 'prefixes' => ['admin.particuliers-resume', 'admin.particuliers.']],
    'artisans'     => ['route' => 'admin.artisans-resume',     'label' => 'Prestataires',       'icon' => 'briefcase', 'group' => 'Utilisateurs', 'prefixes' => ['admin.artisans-resume', 'admin.prestataires.']],
    'candidatures' => ['route' => 'admin.candidatures-resume', 'label' => 'Candidatures',       'icon' => 'shield',    'group' => 'Validation partenaires', 'prefixes' => ['admin.candidatures-resume', 'admin.candidatures.', 'admin.artisans.']],
    'profils'      => ['route' => 'admin.profils-resume',      'label' => 'Profils partenaires','icon' => 'shield',    'group' => 'Validation partenaires', 'prefixes' => ['admin.profils-resume', 'admin.profils.']],
    'attributions' => ['route' => 'admin.attributions-resume', 'label' => 'Attributions',       'icon' => 'clipboard', 'group' => 'Activité', 'prefixes' => ['admin.attributions-resume', 'admin.attributions.']],
    'metiers'      => ['route' => 'admin.metiers-resume',      'label' => 'Catalogue métiers',  'icon' => 'clipboard', 'group' => 'Activité', 'prefixes' => ['admin.metiers-resume', 'admin.metiers.']],
];
$isModuleActive = function (array $module) use ($currentRoute): bool {
    foreach ($module['prefixes'] ?? [$module['route']] as $prefix) {
        if (str_ends_with($prefix, '.')) {
            if (str_starts_with($currentRoute, $prefix)) {
                return true;
            }
        } elseif ($currentRoute === $prefix) {
            return true;
        }
    }

    return false;
};
$menuGroups = [];
foreach ($modules as $id => $m) {
    if ($m['group']) {
        $menuGroups[$m['group']][] = ['id' => $id, 'route' => $m['route'], 'label' => $m['label'], 'icon' => $m['icon'], 'module' => $m];
    }
}

// Icônes SVG
$svgIcon = function ($name, $class = 'admin-icon') {
    $icons = [
        'home'      => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
        'users'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
        'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 11h8"/><path d="M8 16h6"/>',
        'settings'  => '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.08a1.8 1.8 0 0 0-1-.52 1.8 1.8 0 0 0-1.98.36l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1H4a2 2 0 1 1 0-4h.08a1.8 1.8 0 0 0 .52-1 1.8 1.8 0 0 0-.36-1.98l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6V4a2 2 0 1 1 4 0v.08a1.8 1.8 0 0 0 1 .52 1.8 1.8 0 0 0 1.98-.36l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05A1.8 1.8 0 0 0 19.4 9c.22.31.42.65.6 1H20a2 2 0 1 1 0 4h-.08a1.8 1.8 0 0 0-.52 1Z"/>',
        'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
        'search'    => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'menu'      => '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'user-check'=> '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>',
    ];
    return '<svg class="' . $class . '" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$name] ?? $icons['home']) . '</svg>';
};
@endphp
<x-app-layout>
<div class="admin-page" style="--admin-primary: {{ $primary }}; --admin-primary-dark: {{ $primaryDark }}; --admin-primary-text: {{ $primaryText }}; --admin-secondary: {{ $secondary }}; --admin-bg: {{ $bg }}; --admin-muted: {{ $muted }};">
    <style>
        [x-cloak] { display: none !important; }
        .admin-page {
            --sidebar-w: 264px;
            min-height: 100vh;
            color: var(--admin-primary-text);
            background: 
                radial-gradient(1200px 600px at 88% -8%, color-mix(in srgb, var(--admin-secondary) 16%, transparent), transparent 60%),
                radial-gradient(1000px 620px at -6% 4%, color-mix(in srgb, var(--admin-primary) 18%, transparent), transparent 60%),
                linear-gradient(160deg, var(--admin-bg), #ffffff 46%, color-mix(in srgb, var(--admin-primary) 5%, #fff));
        }
        .admin-shell {
            display: grid;
            grid-template-columns: var(--sidebar-w) minmax(0, 1fr);
            min-height: 100vh;
        }
        .admin-sidebar {
            position: sticky; top: 0; height: 100vh; overflow-y: auto; overflow-x: hidden;
            color: #fff;
            background: radial-gradient(600px 260px at 20% -5%, color-mix(in srgb, var(--admin-primary) 55%, transparent), transparent 70%),
                        linear-gradient(185deg, #14101f 0%, #0d0a17 55%, #08060f 100%);
            border-right: 1px solid rgba(255,255,255,.07);
            z-index: 40;
        }
        .admin-sidebar::-webkit-scrollbar { width: .4rem; }
        .admin-sidebar::-webkit-scrollbar-thumb { border-radius: 999px; background: rgba(255,255,255,.18); }
        .admin-brand {
            display: flex; align-items: center; gap: .7rem; padding: 1.15rem 1.15rem;
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
        .admin-brand-name { font-weight: 900; font-size: 1.15rem; letter-spacing: .3px; }
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
        .admin-nav-btn.is-active {
            color: #fff;
            background: linear-gradient(135deg, color-mix(in srgb, var(--admin-primary) 92%, #000), color-mix(in srgb, var(--admin-secondary) 60%, var(--admin-primary-dark)));
        }
        .admin-nav-btn.is-active::before {
            content: ""; position: absolute; left: 0; top: 18%; height: 64%; width: 3px;
            border-radius: 999px; background: #fff;
        }
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
            display: grid; place-items: center;
            border: 1px solid color-mix(in srgb, var(--admin-primary) 16%, transparent);
            background: #fff; color: var(--admin-primary-text);
            transition: transform .15s, box-shadow .2s;
        }
        .admin-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -14px var(--admin-primary); }
        .admin-icon-btn .admin-icon { width: 1.25rem; height: 1.25rem; }
        .admin-avatar {
            width: 42px; height: 42px; border-radius: 12px; overflow: hidden; flex: 0 0 auto;
            display: grid; place-items: center; color: #fff; font-weight: 900;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
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
        .admin-title { font-size: 1.55rem; font-weight: 900; letter-spacing: -.5px; }
        .admin-subtitle { color: var(--admin-muted); font-weight: 600; }
        .admin-action {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .7rem 1.15rem; border-radius: 12px; font-weight: 800; font-size: .9rem;
            color: #fff;
            background: linear-gradient(135deg, var(--admin-primary), color-mix(in srgb, var(--admin-secondary) 45%, var(--admin-primary-dark)));
            box-shadow: 0 18px 34px -20px var(--admin-primary); transition: transform .18s, box-shadow .2s;
            border: none; cursor: pointer; text-decoration: none;
        }
        .admin-action:hover { transform: translateY(-2px); box-shadow: 0 22px 40px -18px var(--admin-primary); }
        .admin-action.secondary {
            background: #fff; color: var(--admin-primary-text);
            border: 1px solid color-mix(in srgb, var(--admin-primary) 22%, transparent); box-shadow: none;
        }
        .admin-action.secondary:hover { background: color-mix(in srgb, var(--admin-primary) 6%, #fff); }
        .admin-toast {
            position: fixed; top: 1.25rem; right: 1.25rem; z-index: 100;
            display: flex; align-items: flex-start; gap: .6rem;
            max-width: 380px; padding: .85rem 1rem; border-radius: 14px;
            font-weight: 700; font-size: .9rem;
            box-shadow: 0 20px 40px -18px rgba(0,0,0,.35); background: #fff;
        }
        .admin-toast.success { border-left: 4px solid #10b981; color: #047857; }
        .admin-toast.error { border-left: 4px solid #ef4444; color: #b91c1c; }
        @media(max-width: 1023px) {
            .admin-shell { grid-template-columns: 1fr; }
            .admin-sidebar { display: none; }
        }
        @media(prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; font-size: .74rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--admin-muted); padding: .7rem 1rem; border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 12%, transparent); }
        .admin-table td { padding: .85rem 1rem; border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 8%, transparent); font-weight: 600; }
        .admin-input-inline { width: 100%; padding: .45rem .6rem; border-radius: 8px; font-size: .88rem; font-weight: 600; border: 1px solid color-mix(in srgb, var(--admin-primary) 16%, transparent); background: color-mix(in srgb, var(--admin-primary) 3%, #fff); color: var(--admin-primary-text); }
        .admin-empty { display: grid; place-items: center; gap: .5rem; padding: 2.5rem 1rem; color: var(--admin-muted); text-align: center; }
        .attribution-card { transition: transform .2s, box-shadow .2s; }
        .attribution-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px -16px rgba(0,0,0,.1); }
        .attribution-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; flex: 0 0 auto; }
        .attribution-timer { display: inline-flex; align-items: center; gap: .4rem; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
        .attribution-timer.urgent { background: #fee2e2; color: #dc2626; }
        .attribution-timer.warning { background: #fef3c7; color: #d97706; }
        .attribution-timer.safe { background: #d1fae5; color: #059669; }
        .admin-action.danger { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 18px 34px -20px #ef4444; }
    </style>

    <div class="admin-shell">
        {{-- SIDEBAR --}}
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <span class="admin-brand-logo">
                    @if ($logo && file_exists(public_path($logo)))
                        <img src="{{ asset($logo) }}" alt="{{ $siteName }}">
                    @else
                        {{ strtoupper(substr($siteName, 0, 1)) }}
                    @endif
                </span>
                <span>
                    <span class="admin-brand-name">{{ $siteName }}</span><br>
                    <span class="admin-brand-sub">Admin</span>
                </span>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-btn {{ $isModuleActive($modules['dashboard']) ? 'is-active' : '' }}">
                    {!! $svgIcon('home') !!}<span>Tableau de bord</span>
                </a>
                @foreach ($menuGroups as $group => $items)
                    <p class="admin-group-label">{{ $group }}</p>
                    @foreach ($items as $item)
                        <a href="{{ route($item['route']) }}" class="admin-nav-btn {{ $isModuleActive($item['module']) ? 'is-active' : '' }}">
                            {!! $svgIcon($item['icon']) !!}<span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                @endforeach
                <div class="my-3 border-t" style="border-color: rgba(255,255,255,.08);"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-btn" style="color: rgba(255,255,255,.5);">
                        {!! $svgIcon('logout') !!}<span>Déconnexion</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- MAIN --}}
        <div class="admin-main">
            <header class="admin-topbar">
                <a href="{{ route('admin.dashboard') }}" class="admin-icon-btn" aria-label="Accueil">
                    {!! $svgIcon('menu') !!}
                </a>
                <div style="flex:1"></div>
                <div class="admin-avatar">
                    @if ($adminPhoto)
                        <img src="{{ $adminPhoto }}" alt="Photo">
                    @else
                        {{ $initial }}
                    @endif
                </div>
            </header>

            <main class="admin-content">
                {{-- Toast --}}
                @if (session('success') || session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
                         class="admin-toast {{ session('success') ? 'success' : 'error' }}" x-cloak>
                        <span>{{ session('success') ?? session('error') }}</span>
                        <button type="button" class="ml-auto" @click="show = false" style="background:none;border:none;cursor:pointer;color:inherit;opacity:.6">✕</button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</x-app-layout>