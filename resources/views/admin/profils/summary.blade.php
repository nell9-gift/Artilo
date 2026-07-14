@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="admin-title">Profils partenaires</h2>
            <p class="admin-subtitle mt-1">État des profils prestataires de la plateforme.</p>
        </div>
        <a href="{{ route('admin.profils.index') }}" class="admin-action">Voir les profils</a>
    </div>
    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl p-4" style="background: color-mix(in srgb, var(--admin-primary) 7%, white)">
            <p class="font-black text-lg">{{ $incompleteProfilesCount }} profil(s) incomplet(s)</p>
            <p class="mt-1 text-sm" style="color: var(--admin-muted)">Documents, photos, adresse ou description à compléter.</p>
        </div>
        <div class="rounded-xl p-4" style="background: color-mix(in srgb, var(--admin-secondary) 10%, white)">
            <p class="font-black text-lg">{{ $profileCount }} profil(s) enrichi(s)</p>
            <p class="mt-1 text-sm" style="color: var(--admin-muted)">Prêts pour la vitrine publique après vérification.</p>
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
            <p class="text-sm" style="color: var(--admin-muted)">Refusés</p>
        </div>
    </div>
</div>
@endsection
