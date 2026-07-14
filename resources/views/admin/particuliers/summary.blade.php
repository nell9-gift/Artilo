{{-- Résumé Particuliers --}}
@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="admin-title">👥 Gestion des Particuliers</h2>
            <p class="admin-subtitle mt-1"><span class="font-black" style="color: var(--admin-primary)">{{ $customerCount }}</span> client(s) inscrit(s).</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.particuliers.index') }}" class="admin-action">📋 Voir tous les particuliers</a>
            <a href="{{ route('admin.particuliers.export') }}" class="admin-action secondary" style="padding: .6rem 1rem; font-size: .8rem;">📥 Export CSV</a>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl p-5 text-center" style="background: color-mix(in srgb, var(--admin-primary) 8%, white)">
            <p class="text-3xl font-black" style="color: var(--admin-primary)">{{ $customerCount }}</p>
            <p class="text-sm font-semibold mt-1" style="color: var(--admin-muted)">Total inscrits</p>
        </div>
        <div class="rounded-xl p-5 text-center" style="background: color-mix(in srgb, #059669 8%, white)">
            <p class="text-3xl font-black" style="color: #059669">{{ $particuliersActifs }}</p>
            <p class="text-sm font-semibold mt-1" style="color: var(--admin-muted)">Actifs (≥1 demande)</p>
        </div>
        <div class="rounded-xl p-5 text-center" style="background: color-mix(in srgb, var(--admin-secondary) 8%, white)">
            <p class="text-3xl font-black" style="color: var(--admin-secondary)">{{ $particuliersNouveauxMois }}</p>
            <p class="text-sm font-semibold mt-1" style="color: var(--admin-muted)">Nouveaux ce mois</p>
        </div>
        <div class="rounded-xl p-5 text-center" style="background: color-mix(in srgb, #2563EB 8%, white)">
            <p class="text-3xl font-black" style="color: #2563EB">{{ $totalDemandes }}</p>
            <p class="text-sm font-semibold mt-1" style="color: var(--admin-muted)">Demandes totales</p>
        </div>
    </div>
    @if($particuliersRecents->isNotEmpty())
    <div class="mt-6">
        <h3 class="font-bold mb-3">🆕 Derniers inscrits</h3>
        <div class="overflow-hidden rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
            @foreach($particuliersRecents as $p)
            <div class="flex items-center justify-between p-4 border-b last:border-b-0" style="border-color:color-mix(in srgb,var(--admin-primary)8%,transparent);background:#fff;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,var(--admin-primary),var(--admin-secondary));">
                        {{ strtoupper(substr($p->name,0,1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm">{{ $p->name }}</p>
                        <p class="text-xs" style="color:var(--admin-muted)">{{ $p->email }} · {{ $p->total_missions ?? 0 }} mission(s)</p>
                    </div>
                </div>
                <span class="text-xs" style="color:var(--admin-muted)">{{ $p->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection