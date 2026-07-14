@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5"><div><h2 class="admin-title">Catalogue des métiers</h2><p class="admin-subtitle mt-1">{{ $metiers->count() }} métier(s) disponible(s).</p></div><a href="{{ route('admin.metiers.index') }}" class="admin-action">Gérer le catalogue</a></div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($metiers as $metier)
            <div class="rounded-xl p-4" style="background:color-mix(in srgb,var(--admin-primary) 5%,white);border:1px solid color-mix(in srgb,var(--admin-primary) 12%,transparent)"><p class="font-black">{{ $metier->nom }}</p><p class="text-sm mt-1" style="color:var(--admin-muted)">{{ $metier->artisans_count }} prestataire(s)</p></div>
        @empty
            <p class="admin-empty">Le catalogue ne contient encore aucun métier.</p>
        @endforelse
    </div>
</div>
@endsection
