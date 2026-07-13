@php
    $siteName    = $settings['site_name'] ?? 'Artilo';
    $primary     = $settings['color_primary'] ?? '#7C3AED';
    $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';
    $primaryText = $settings['color_primary_900'] ?? '#2E1065';
    $secondary   = $settings['color_secondary'] ?? '#D97706';
    $muted       = $settings['color_muted'] ?? '#6B5B95';
@endphp

<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto" style="color: {{ $primaryText }};">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-black">Gestion des attributions</h1>
                <p class="text-sm mt-1" style="color: {{ $muted }};">
                    Gérez l'attribution des missions aux prestataires.
                </p>
            </div>
            <a href="{{ route('admin.MaPage') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm"
               style="background: color-mix(in srgb, {{ $primary }} 8%, #fff); color: {{ $primary }};">
                &larr; Dashboard
            </a>
        </div>

        {{-- Toast --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-xl font-bold text-sm" style="background: color-mix(in srgb, #10b981 12%, #fff); color: #047857;">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div class="mb-4 p-4 rounded-xl font-bold text-sm" style="background: color-mix(in srgb, #0ea5e9 12%, #fff); color: #0369a1;">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        {{-- Demandes en attente --}}
        <div class="rounded-2xl border overflow-hidden mb-8" style="border-color: color-mix(in srgb, {{ $primary }} 12%, transparent); background: rgba(255,255,255,.9);">
            <div class="p-5 border-b flex items-center justify-between gap-3" style="border-color: color-mix(in srgb, {{ $primary }} 10%, transparent);">
                <div>
                    <h2 class="font-black text-lg">📋 Demandes en attente</h2>
                    <p class="text-sm" style="color: {{ $muted }};">{{ $demandesEnAttente->total() }} demande(s)</p>
                </div>
                <span class="text-sm font-bold px-3 py-1 rounded-full" style="background: color-mix(in srgb, {{ $secondary }} 14%, #fff); color: {{ $secondary }};">
                    ⏳ Action requise
                </span>
            </div>

            @if($demandesEnAttente->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="color: {{ $muted }};">
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">#</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Client</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Métier</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Date</th>
                                <th class="text-center font-bold uppercase text-xs tracking-wide p-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandesEnAttente as $demande)
                                <tr class="border-t" style="border-color: color-mix(in srgb, {{ $primary }} 8%, transparent);">
                                    <td class="p-3 font-black">#{{ $demande->id }}</td>
                                    <td class="p-3">
                                        <p class="font-bold">{{ $demande->particulier->name ?? 'Client' }}</p>
                                        <p class="text-xs" style="color: {{ $muted }};">{{ $demande->particulier->email ?? '' }}</p>
                                    </td>
                                    <td class="p-3">
                                        <span class="text-sm font-bold px-2.5 py-1 rounded-full" style="background: color-mix(in srgb, {{ $primary }} 12%, #fff); color: {{ $primary }};">
                                            {{ $demande->metier->nom ?? $demande->metier_requis ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <p class="font-bold">{{ $demande->created_at->format('d/m/Y') }}</p>
                                        <p class="text-xs" style="color: {{ $muted }};">{{ $demande->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('admin.attributions.show', $demande) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-bold text-xs"
                                           style="background: linear-gradient(135deg, {{ $primary }}, {{ $primaryDark }}); color: #fff;">
                                            🔍 Voir & attribuer
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($demandesEnAttente, 'links'))
                    <div class="p-3 border-t" style="border-color: color-mix(in srgb, {{ $primary }} 8%, transparent);">
                        {{ $demandesEnAttente->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center">
                    <p class="text-3xl mb-2">✅</p>
                    <p class="font-black">Aucune demande en attente</p>
                    <p class="text-sm mt-1" style="color: {{ $muted }};">Toutes les demandes ont été traitées.</p>
                </div>
            @endif
        </div>

        {{-- Missions affectées --}}
        <div class="rounded-2xl border overflow-hidden" style="border-color: color-mix(in srgb, {{ $primary }} 12%, transparent); background: rgba(255,255,255,.9);">
            <div class="p-5 border-b flex items-center justify-between gap-3" style="border-color: color-mix(in srgb, {{ $primary }} 10%, transparent);">
                <div>
                    <h2 class="font-black text-lg">⏳ Missions en attente de réponse</h2>
                    <p class="text-sm" style="color: {{ $muted }};">{{ $missionsAffectees->total() }} mission(s)</p>
                </div>
                <span class="text-sm font-bold px-3 py-1 rounded-full" style="background: color-mix(in srgb, #0ea5e9 14%, #fff); color: #0ea5e9;">
                    En attente prestataire
                </span>
            </div>

            @if($missionsAffectees->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="color: {{ $muted }};">
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">#</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Client</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Prestataire</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Statut</th>
                                <th class="text-left font-bold uppercase text-xs tracking-wide p-3">Expire</th>
                                <th class="text-center font-bold uppercase text-xs tracking-wide p-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($missionsAffectees as $mission)
                                <tr class="border-t" style="border-color: color-mix(in srgb, {{ $primary }} 8%, transparent);">
                                    <td class="p-3 font-black">#{{ $mission->id }}</td>
                                    <td class="p-3">
                                        <p class="font-bold">{{ $mission->particulier->name ?? 'Client' }}</p>
                                    </td>
                                    <td class="p-3">
                                        @if($mission->artisan)
                                            <p class="font-bold">{{ $mission->artisan->user->name ?? 'Prestataire' }}</p>
                                            <p class="text-xs" style="color: {{ $muted }};">Score: {{ $mission->artisan->score_interne ?? 'N/A' }}</p>
                                        @else
                                            <span style="color: {{ $muted }};">—</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <span class="text-sm font-bold">{{ $mission->statut_libelle }}</span>
                                    </td>
                                    <td class="p-3">
                                        @if($mission->expire_le)
                                            @php $minutes = now()->diffInMinutes($mission->expire_le, false); @endphp
                                            <span class="font-bold" style="color: {{ $minutes <= 0 ? '#dc2626' : $secondary }};">
                                                {{ $minutes <= 0 ? 'Expiré' : $minutes . ' min' }}
                                            </span>
                                        @else
                                            <span style="color: {{ $muted }};">N/A</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.attributions.annuler', $mission) }}" method="POST"
                                                  onsubmit="return confirm('Annuler cette attribution ?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg font-bold text-xs"
                                                        style="background: color-mix(in srgb, #ef4444 12%, #fff); color: #dc2626;">
                                                    ❌ Annuler
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($missionsAffectees, 'links'))
                    <div class="p-3 border-t" style="border-color: color-mix(in srgb, {{ $primary }} 8%, transparent);">
                        {{ $missionsAffectees->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center">
                    <p class="text-3xl mb-2">⏳</p>
                    <p class="font-black">Aucune mission en attente</p>
                    <p class="text-sm mt-1" style="color: {{ $muted }};">Toutes les missions ont reçu une réponse.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>