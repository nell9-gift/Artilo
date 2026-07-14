{{--
============================================================
   ARTILO — Dashboard Administrateur
   Anciennement dans le one-page admin/MaPage
   Maintenant page indépendante !
============================================================ --}}
@extends('admin.layouts.app')

@php
$svgIcon = function ($name, $class = 'admin-icon') {
    $icons = [
        'home'      => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
        'users'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
        'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 11h8"/><path d="M8 16h6"/>',
        'wallet'    => '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0 0 4h15a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5"/><path d="M16 12h.01"/>',
        'coins'     => '<circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/>',
        'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    ];
    return '<svg class="' . $class . '" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$name] ?? $icons['home']) . '</svg>';
};
$stats = [
    ['id' => 'artisans',    'label' => 'Prestataires', 'value' => $totalArtisans, 'hint' => 'tous statuts',        'icon' => 'briefcase', 'trend' => '+8%'],
    ['id' => 'clients',     'label' => 'Particuliers',  'value' => $customerCount, 'hint' => 'comptes clients',     'icon' => 'users',     'trend' => '+12%'],
    ['id' => 'pending',     'label' => 'Candidatures',  'value' => $pendingCount,  'hint' => 'en attente',          'icon' => 'shield',    'trend' => 'a traiter'],
    ['id' => 'missions',    'label' => 'Missions/jour', 'value' => 0,              'hint' => 'module a connecter',  'icon' => 'clipboard', 'trend' => '--'],
    ['id' => 'revenus',     'label' => 'Revenus',       'value' => '0 F',          'hint' => 'paiements',           'icon' => 'wallet',    'trend' => '--'],
    ['id' => 'commissions', 'label' => 'Commissions',   'value' => '0 F',          'hint' => 'finance',             'icon' => 'coins',     'trend' => '--'],
];
@endphp

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="admin-title">Bonjour, {{ Auth::user()?->name ?? 'Admin' }}</h1>
            <p class="admin-subtitle mt-1">Vue d'ensemble de la plateforme {{ $settings['site_name'] ?? 'Artilo' }}.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.artisans.index') }}" class="admin-action">Voir les candidatures</a>
            <a href="{{ route('admin.profils.index') }}" class="admin-action secondary">Profils partenaires</a>
        </div>
    </div>

    {{-- KPIs --}}
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

    {{-- Graphiques --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <article class="admin-card p-5 lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black">Activité des missions</h2>
                    <p class="text-sm" style="color: var(--admin-muted)">Évolution sur les 7 derniers mois</p>
                </div>
                <span class="admin-status">Temps réel</span>
            </div>
            <div class="mt-4" style="height: 280px"><canvas id="chartMissions"></canvas></div>
        </article>
        <article class="admin-card p-5">
            <h2 class="text-lg font-black">Répartition des demandes</h2>
            <p class="text-sm" style="color: var(--admin-muted)">Par statut</p>
            <div class="mt-4" style="height: 280px"><canvas id="chartStatuts"></canvas></div>
        </article>
    </div>

    {{-- Métiers + Candidatures --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <article class="admin-card p-5">
            <h2 class="text-lg font-black">Métiers demandés</h2>
            <p class="text-sm" style="color: var(--admin-muted)">Top interventions</p>
            <div class="mt-4" style="height: 260px"><canvas id="chartMetiers"></canvas></div>
        </article>
        <article class="admin-card p-5 lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black">Candidatures récentes</h2>
                <a href="{{ route('admin.artisans.index') }}" class="text-sm font-black" style="color: var(--admin-primary)">Tout voir</a>
            </div>
            <div class="mt-4 divide-y" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                @forelse ($latestCandidates as $artisan)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                            <p class="text-sm" style="color: var(--admin-muted)">{{ ucfirst($artisan->profession) }} - {{ $artisan->intervention_area }}</p>
                        </div>
                        <a href="{{ route('admin.artisans.index') }}" class="admin-status">Vérifier</a>
                    </div>
                @empty
                    <p class="py-5 text-sm" style="color: var(--admin-muted)">Aucune candidature en attente pour le moment.</p>
                @endforelse
            </div>
        </article>
    </div>
</div>

<style>
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
    .admin-status {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .35rem .8rem; border-radius: 999px; font-size: .78rem; font-weight: 800;
        color: var(--admin-secondary);
        background: color-mix(in srgb, var(--admin-secondary) 14%, #fff);
        border: 1px solid color-mix(in srgb, var(--admin-secondary) 30%, transparent);
    }
</style>

@push('scripts')
<script>
(function () {
    function readVar(el, name, fallback) { var v = getComputedStyle(el).getPropertyValue(name); return (v && v.trim()) || fallback; }
    var charts = {};
    if (typeof Chart === 'undefined') return;
    var page = document.querySelector('.admin-page'); if (!page) return;
    var primary = readVar(page, '--admin-primary', '#7C3AED'), secondary = readVar(page, '--admin-secondary', '#D97706'), muted = readVar(page, '--admin-muted', '#6B5B95');
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily; Chart.defaults.color = muted;
    var elLine = document.getElementById('chartMissions');
    if (elLine && !charts.line) { var ctx = elLine.getContext('2d'); var grad = ctx.createLinearGradient(0, 0, 0, 260); grad.addColorStop(0, primary + '55'); grad.addColorStop(1, primary + '00'); charts.line = new Chart(ctx, { type: 'line', data: { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil'], datasets: [{ label: 'Missions', data: [12, 19, 15, 27, 24, 33, 41], borderColor: primary, backgroundColor: grad, fill: true, tension: .4, borderWidth: 3, pointRadius: 3 }, { label: 'Demandes', data: [18, 22, 20, 30, 29, 38, 47], borderColor: secondary, backgroundColor: 'transparent', fill: false, tension: .4, borderWidth: 3, borderDash: [6, 5] }] }, options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: { display: false } }, y: { grid: { color: 'rgba(0,0,0,.06)' }, beginAtZero: true } } } }); }
    var elDo = document.getElementById('chartStatuts');
    if (elDo && !charts.donut) { charts.donut = new Chart(elDo.getContext('2d'), { type: 'doughnut', data: { labels: ['En attente', 'Affectées', 'En cours', 'Terminées'], datasets: [{ data: [8, 12, 15, 34], backgroundColor: [secondary, '#0ea5e9', primary, '#10b981'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '64%' } }); }
    var elBar = document.getElementById('chartMetiers');
    if (elBar && !charts.bar) { charts.bar = new Chart(elBar.getContext('2d'), { type: 'bar', data: { labels: ['Plomberie', 'Élec', 'Peinture', 'Maçonnerie', 'Menuiserie'], datasets: [{ label: 'Demandes', data: [24, 31, 18, 14, 9], backgroundColor: primary, borderRadius: 8 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { color: 'rgba(0,0,0,.06)' }, beginAtZero: true } } } }); }
})();
</script>
@endpush