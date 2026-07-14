@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="admin-title">Candidatures — en attente</h2>
            <p class="admin-subtitle mt-1">{{ $pendingCount }} candidature(s) à valider ou refuser.</p>
        </div>
        <a href="{{ route('admin.artisans.index') }}" class="admin-action">Voir les candidatures</a>
    </div>
    <div class="mt-5 overflow-hidden rounded-xl border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
        @forelse ($latestCandidates as $artisan)
            <div class="grid gap-3 border-b bg-white p-4 md:grid-cols-[1fr_1fr_auto]" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                <div>
                    <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                    <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->user->email ?? 'Email non renseigné' }}</p>
                </div>
                <div>
                    <p class="font-bold">{{ ucfirst($artisan->profession) }}</p>
                    <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->intervention_area }}</p>
                </div>
                <span class="admin-status self-center">En attente</span>
            </div>
        @empty
            <p class="bg-white p-5 text-sm" style="color: var(--admin-muted)">Aucune candidature récente.</p>
        @endforelse
    </div>
</div>
@endsection
