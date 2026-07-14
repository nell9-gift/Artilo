@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div><h2 class="admin-title">Attributions de missions</h2><p class="admin-subtitle mt-1">Suivez les demandes à attribuer et les réponses en attente.</p></div>
        <a href="{{ route('admin.attributions.index') }}" class="admin-action">Gérer les attributions</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 mb-6">
        <div class="rounded-xl p-5" style="background:color-mix(in srgb,var(--admin-secondary) 10%,white)"><p class="text-3xl font-black" style="color:var(--admin-secondary)">{{ $demandesEnAttente->total() }}</p><p class="text-sm font-semibold mt-1" style="color:var(--admin-muted)">Demandes à attribuer</p></div>
        <div class="rounded-xl p-5" style="background:color-mix(in srgb,var(--admin-primary) 10%,white)"><p class="text-3xl font-black" style="color:var(--admin-primary)">{{ $missionsAffectees->total() }}</p><p class="text-sm font-semibold mt-1" style="color:var(--admin-muted)">Réponses prestataires attendues</p></div>
    </div>
    <h3 class="font-bold mb-3">Dernières demandes en attente</h3>
    @forelse($demandesEnAttente as $mission)
        <div class="flex items-center justify-between gap-3 py-3 border-b" style="border-color:color-mix(in srgb,var(--admin-primary) 10%,transparent)"><div><p class="font-bold">{{ ucfirst($mission->metier_requis) }}</p><p class="text-sm" style="color:var(--admin-muted)">{{ $mission->particulier?->name ?? 'Particulier' }} · {{ $mission->adresse }}</p></div><a href="{{ route('admin.attributions.show', $mission) }}" class="admin-action secondary">Attribuer</a></div>
    @empty
        <p class="admin-empty">Aucune demande en attente.</p>
    @endforelse
</div>
@endsection
