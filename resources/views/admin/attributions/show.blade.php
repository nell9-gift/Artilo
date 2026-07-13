@php
    $siteName    = $settings['site_name'] ?? 'Artilo';
    $primary     = $settings['color_primary'] ?? '#7C3AED';
    $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';
    $primaryText = $settings['color_primary_900'] ?? '#2E1065';
    $secondary   = $settings['color_secondary'] ?? '#D97706';
    $bg          = $settings['color_bg_2'] ?? '#FAF8FF';
    $muted       = $settings['color_muted'] ?? '#6B5B95';
@endphp

<x-app-layout>
    <div class="p-6 max-w-6xl mx-auto" style="color: {{ $primaryText }};">
        <style>
            .match-bar { height: 8px; border-radius: 999px; background: #e5e7eb; overflow: hidden; }
            .match-bar-fill { height: 100%; border-radius: 999px; transition: width .6s cubic-bezier(.2,.7,.3,1); }
            .candidate-card { transition: transform .2s, box-shadow .2s; }
            .candidate-card:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -18px rgba(0,0,0,.2); }
            .candidate-card.selected { border-color: {{ $primary }} !important; box-shadow: 0 0 0 3px color-mix(in srgb, {{ $primary }} 25%, transparent); }
        </style>

        {{-- En-tête --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('admin.attributions.index') }}" class="text-sm font-bold hover:underline" style="color: {{ $primary }};">&larr; Retour aux attributions</a>
                <h1 class="text-2xl font-black mt-1">Mission #{{ $mission->id }}</h1>
                <p class="text-sm" style="color: {{ $muted }};">
                    Demande de <strong>{{ $mission->particulier->name ?? 'Client' }}</strong>
                    &middot; {{ $mission->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold"
                  style="background: color-mix(in srgb, {{ $secondary }} 14%, #fff); color: {{ $secondary }};">
                ⏳ En attente d'attribution
            </span>
        </div>

        {{-- Détails de la mission --}}
        <div class="rounded-2xl p-5 mb-6 border" style="border-color: color-mix(in srgb, {{ $primary }} 12%, transparent); background: rgba(255,255,255,.9); box-shadow: 0 8px 24px -12px rgba(0,0,0,.08);">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: {{ $muted }};">Métier requis</p>
                    <p class="font-black text-lg">{{ $mission->metier->nom ?? $mission->metier_requis ?? 'Non spécifié' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: {{ $muted }};">Adresse</p>
                    <p class="font-bold">{{ $mission->adresse ?? 'Non renseignée' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide" style="color: {{ $muted }};">Budget prévisionnel</p>
                    <p class="font-black text-lg">{{ $mission->budget_previsionnel ? number_format($mission->budget_previsionnel, 0, ',', ' ') . ' F' : 'Non spécifié' }}</p>
                </div>
            </div>
            @if($mission->description)
                <div class="mt-4 pt-4 border-t" style="border-color: color-mix(in srgb, {{ $primary }} 10%, transparent);">
                    <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: {{ $muted }};">Description</p>
                    <p class="text-sm">{{ $mission->description }}</p>
                </div>
            @endif
        </div>

        {{-- Titre sélection --}}
        <div class="mb-5">
            <h2 class="text-xl font-black">Top 3 des prestataires recommandés</h2>
            <p class="text-sm" style="color: {{ $muted }};">
                Basé sur le niveau, l'expérience, les évaluations et la proximité géographique.
            </p>
        </div>

        {{-- Liste des candidats --}}
        @if($candidats->isEmpty())
            <div class="rounded-2xl p-8 text-center border-2 border-dashed" style="border-color: color-mix(in srgb, {{ $primary }} 16%, transparent); background: rgba(255,255,255,.6);">
                <p class="text-4xl mb-2">😕</p>
                <p class="font-black text-lg">Aucun prestataire disponible</p>
                <p class="text-sm mt-1" style="color: {{ $muted }};">
                    Aucun artisan validé ne correspond à cette demande pour le moment.
                    <br>Vérifiez que des prestataires sont inscrits dans ce métier et disponibles.
                </p>
                <a href="{{ route('admin.attributions.index') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl font-bold text-white"
                   style="background: linear-gradient(135deg, {{ $primary }}, {{ $primaryDark }});">
                    Retour aux attributions
                </a>
            </div>
        @else
            <form id="attribution-form" method="POST" action="{{ route('admin.attributions.attribuer', $mission) }}">
                @csrf
                <input type="hidden" name="artisan_id" id="selected_artisan_id" value="">

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($candidats as $index => $candidat)
                        @php
                            $badge = match(true) {
                                $index === 0 => ['text' => '🥇 Meilleur choix', 'color' => '#D97706'],
                                $index === 1 => ['text' => '🥈 Très bon profil', 'color' => '#6B7280'],
                                default => ['text' => '🥉 Bon profil', 'color' => '#92400E'],
                            };
                            $photoUrl = $candidat->user->profile_photo ? asset('storage/'.$candidat->user->profile_photo) : null;
                            $initiale = strtoupper(substr($candidat->user->name ?? '?', 0, 1));
                        @endphp

                        <div class="candidate-card rounded-2xl p-5 border-2 cursor-pointer transition-all"
                             style="border-color: color-mix(in srgb, {{ $primary }} 12%, transparent); background: #fff;"
                             x-data
                             onclick="document.getElementById('selected_artisan_id').value = '{{ $candidat->id }}'; document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected')); this.classList.add('selected'); document.getElementById('btn-valider').disabled = false; document.getElementById('btn-valider').classList.remove('opacity-50', 'cursor-not-allowed');">

                            {{-- Badge --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black px-2.5 py-1 rounded-full"
                                      style="background: color-mix(in srgb, {{ $badge['color'] }} 14%, #fff); color: {{ $badge['color'] }};">
                                    {{ $badge['text'] }}
                                </span>
                                <span class="font-black text-sm" style="color: {{ $primary }};">
                                    Score {{ $candidat->score_matching }}/100
                                </span>
                            </div>

                            {{-- Avatar + Nom --}}
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center font-black text-white text-lg"
                                     style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ $initiale }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-black">{{ $candidat->user->name ?? 'Prestataire' }}</p>
                                    <p class="text-sm" style="color: {{ $muted }};">
                                        {{ $candidat->main_profession ?? $candidat->profession ?? 'Prestataire' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Barre de matching --}}
                            <div class="mb-4">
                                <div class="match-bar">
                                    <div class="match-bar-fill" style="width: {{ $candidat->score_matching }}%; background: linear-gradient(90deg, {{ $primary }}, {{ $secondary }});"></div>
                                </div>
                            </div>

                            {{-- Détails --}}
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span style="color: {{ $muted }};">Niveau</span>
                                    <span class="font-bold">{{ $candidat->niveau_libelle ?? ($candidat->niveau ?? 'Non défini') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: {{ $muted }};">Note</span>
                                    <span class="font-bold">{{ $candidat->average_rating ? number_format($candidat->average_rating, 1) . ' / 5' : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: {{ $muted }};">Missions</span>
                                    <span class="font-bold">{{ $candidat->missions_acceptees_count ?? 0 }} réalisée(s)</span>
                                </div>
                                @if(isset($candidat->distance_km))
                                <div class="flex justify-between">
                                    <span style="color: {{ $muted }};">Distance</span>
                                    <span class="font-bold">{{ number_format($candidat->distance_km, 1) }} km</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span style="color: {{ $muted }};">Zone</span>
                                    <span class="font-bold">{{ $candidat->intervention_area ?? 'Non précisée' }}</span>
                                </div>
                            </div>

                            @if($candidat->score_interne)
                                <div class="mt-3 pt-3 border-t text-center" style="border-color: color-mix(in srgb, {{ $primary }} 10%, transparent);">
                                    <span class="text-xs font-bold" style="color: {{ $muted }};">Score interne: {{ $candidat->score_interne }}/100</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Bouton Valider --}}
                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 p-5 rounded-2xl border"
                     style="border-color: color-mix(in srgb, {{ $primary }} 12%, transparent); background: rgba(255,255,255,.8);">
                    <div>
                        <p class="font-black">Sélectionnez un prestataire pour attribuer la mission</p>
                        <p class="text-sm" style="color: {{ $muted }};">
                            Une notification sera envoyée au prestataire choisi. Il aura
                            <strong>{{ config('artilo.delai_acceptation_minutes', 20) }} minutes</strong> pour répondre.
                        </p>
                    </div>
                    <button type="submit" id="btn-valider"
                            class="admin-action opacity-50 cursor-not-allowed"
                            style="padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; color: #fff; background: linear-gradient(135deg, {{ $primary }}, {{ $primaryDark }}); box-shadow: 0 18px 34px -20px {{ $primary }};"
                            disabled>
                        ✅ Valider l'attribution
                    </button>
                </div>
            </form>
        @endif

        {{-- Légende des scores --}}
        <div class="mt-6 p-4 rounded-xl text-sm" style="background: color-mix(in srgb, {{ $primary }} 5%, #fff); color: {{ $muted }};">
            <p class="font-bold mb-1">💡 Comment est calculé le score ?</p>
            <p>
                <strong>Niveau</strong> (60 pts max) &middot;
                <strong>Évaluations</strong> (20 pts max) &middot;
                <strong>Expérience</strong> (20 pts max) &middot;
                <strong>Proximité</strong> (20 pts max)
            </p>
        </div>
    </div>

    {{-- Script pour gérer la sélection au clic --}}
    <script>
        document.querySelectorAll('.candidate-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('btn-valider').disabled = false;
                document.getElementById('btn-valider').classList.remove('opacity-50', 'cursor-not-allowed');
            });
        });
    </script>
</x-app-layout>