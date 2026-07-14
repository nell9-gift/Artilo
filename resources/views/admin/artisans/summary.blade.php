@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="admin-title">Gestion des Prestataires</h2>
            <p class="admin-subtitle mt-1"><span class="font-black" style="color: var(--admin-primary)">{{ $totalArtisans }}</span> prestataire(s) inscrit(s).</p>
        </div>
        <a href="{{ route('admin.prestataires.index') }}" class="admin-action">Voir tous les prestataires</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-4">
        <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, var(--admin-primary) 8%, white)">
            <p class="text-2xl font-black" style="color: var(--admin-primary)">{{ $totalArtisans }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1">Total</p>
        </div>
        <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, #059669 8%, white)">
            <p class="text-2xl font-black" style="color: #059669">{{ $approvedCount }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1">Validés</p>
        </div>
        <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, var(--admin-secondary) 8%, white)">
            <p class="text-2xl font-black" style="color: var(--admin-secondary)">{{ $pendingCount }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1">En attente</p>
        </div>
        <div class="rounded-xl p-4 text-center" style="background: color-mix(in srgb, #DC2626 8%, white)">
            <p class="text-2xl font-black" style="color: #DC2626">{{ $rejectedCount }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1">Refusés</p>
        </div>
    </div>
    @if($professions->isNotEmpty())
    <div class="mt-5">
        <h3 class="font-bold mb-3">Top 5 des métiers</h3>
        <div class="space-y-2">
            @foreach($professions as $prof)
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-600">{{ ucfirst($prof->profession) }}</span>
                <span class="text-sm font-bold" style="color: var(--admin-primary)">{{ $prof->total }} artisan(s)</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($latestArtisans->isNotEmpty())
    <div class="mt-5">
        <h3 class="font-bold mb-3">Dernières inscriptions</h3>
        <div class="space-y-3">
            @foreach($latestArtisans as $artisan)
            <div class="flex items-center justify-between p-3 rounded-xl" style="background: rgba(124,58,237,0.04)">
                <div>
                    <p class="font-bold">{{ $artisan->user->name ?? 'Artisan' }}</p>
                    <p class="text-sm text-gray-500">{{ ucfirst($artisan->profession) }} · {{ $artisan->intervention_area }}</p>
                </div>
                <span class="text-xs text-gray-400">{{ $artisan->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="mt-5 flex gap-3">
        <a href="{{ route('admin.prestataires.index') }}" class="admin-action">Gérer les prestataires</a>
        <a href="{{ route('admin.profils.index') }}" class="admin-action secondary">Voir les profils</a>
    </div>
</div>
@endsection
