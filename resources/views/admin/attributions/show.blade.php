@php
    $siteName    = $settings['site_name'] ?? 'Artilo';
    $primary     = $settings['color_primary'] ?? '#7C3AED';
    $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';
    $primaryLight = $settings['color_primary_light'] ?? '#EDE9FE';
    $primaryText = $settings['color_primary_900'] ?? '#2E1065';
    $secondary   = $settings['color_secondary'] ?? '#D97706';
    $bg          = $settings['color_bg_2'] ?? '#FAF8FF';
    $muted       = $settings['color_muted'] ?? '#6B5B95';
@endphp

<x-app-layout>
    <div class="min-h-screen py-8" style="background: linear-gradient(135deg, {{ $bg }} 0%, #FFFFFF 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <style>
                .candidate-card {
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    border: 2px solid transparent;
                }
                
                .candidate-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 20px 40px -12px rgba(124, 58, 237, 0.15);
                    border-color: {{ $primary }};
                }
                
                .candidate-card.selected {
                    transform: translateY(-4px);
                    box-shadow: 0 20px 40px -12px rgba(124, 58, 237, 0.25);
                    border-color: {{ $primary }} !important;
                    background: linear-gradient(135deg, #FFFFFF 0%, {{ $primaryLight }} 100%);
                }
                
                .match-bar {
                    height: 10px;
                    border-radius: 999px;
                    background: #F3F4F6;
                    overflow: hidden;
                }
                
                .match-bar-fill {
                    height: 100%;
                    border-radius: 999px;
                    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                }
                
                .btn-primary {
                    background: linear-gradient(135deg, {{ $primary }}, {{ $primaryDark }});
                    color: white;
                    padding: 12px 24px;
                    border-radius: 12px;
                    font-weight: 700;
                    transition: all 0.3s;
                    box-shadow: 0 4px 15px -3px rgba(124, 58, 237, 0.3);
                }
                
                .btn-primary:hover:not(:disabled) {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px -5px rgba(124, 58, 237, 0.4);
                }
                
                .btn-primary:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                    transform: none;
                }
            </style>

            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-sm mb-8">
                <a href="{{ route('admin.attributions.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors font-medium">
                    ← Attributions
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-semibold">Mission #{{ $mission->id }}</span>
            </nav>

            {{-- En-tête principal --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <h1 class="text-3xl font-black" style="color: {{ $primaryText }};">
                        Attribution de mission #{{ $mission->id }}
                    </h1>
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold"
                          style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                        ⏳ En attente d'attribution
                    </span>
                </div>
                <p class="text-gray-600">
                    Demandé par <span class="font-semibold">{{ $mission->particulier->name ?? 'Client' }}</span> • 
                    {{ $mission->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>

            {{-- Détails de la mission --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">📋 Détails de la mission</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Métier requis</p>
                        <p class="font-bold text-gray-900">
                            {{ $mission->metier->nom ?? $mission->metier_requis ?? 'Non spécifié' }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Adresse</p>
                        <p class="font-medium text-gray-900">
                            {{ $mission->adresse ?? 'Non renseignée' }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Budget prévisionnel</p>
                        <p class="font-bold text-gray-900">
                            {{ $mission->budget_previsionnel ? number_format($mission->budget_previsionnel, 0, ',', ' ') . ' FCFA' : 'Non spécifié' }}
                        </p>
                    </div>
                </div>
                
                @if($mission->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</p>
                        <p class="text-gray-700">{{ $mission->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Titre de la section --}}
            <div class="mb-6">
                <h2 class="text-xl font-black text-gray-900">Top 3 des prestataires recommandés</h2>
                <p class="text-gray-500 mt-1">
                    Basé sur le niveau, l'expérience, les évaluations et la proximité géographique
                </p>
            </div>

            @if($candidats->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border-2 border-dashed border-gray-200 p-12 text-center">
                    <p class="text-4xl mb-4">😕</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun prestataire disponible</h3>
                    <p class="text-gray-500 mb-6">
                        Aucun artisan validé ne correspond à cette demande pour le moment.
                    </p>
                    <a href="{{ route('admin.attributions.index') }}" class="btn-primary inline-flex items-center">
                        ← Retour aux attributions
                    </a>
                </div>
            @else
                <form id="attribution-form" method="POST" action="{{ route('admin.attributions.attribuer', $mission) }}">
                    @csrf
                    <input type="hidden" name="artisan_id" id="selected_artisan_id" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($candidats as $index => $candidat)
                            @php
                                $badge = match($index) {
                                    0 => ['text' => '🥇 Meilleur choix', 'color' => '#D97706'],
                                    1 => ['text' => '🥈 Très bon profil', 'color' => '#6B7280'],
                                    default => ['text' => '🥉 Bon profil', 'color' => '#92400E'],
                                };
                                $photoUrl = $candidat->user->profile_photo ? asset('storage/'.$candidat->user->profile_photo) : null;
                                $initiale = strtoupper(substr($candidat->user->name ?? '?', 0, 1));
                            @endphp

                            <div class="candidate-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 cursor-pointer"
                                 onclick="selectCandidate(this, '{{ $candidat->id }}')">
                                
                                {{-- Badge et score --}}
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-black px-3 py-1 rounded-full"
                                          style="background: color-mix(in srgb, {{ $badge['color'] }} 14%, #fff); color: {{ $badge['color'] }};">
                                        {{ $badge['text'] }}
                                    </span>
                                    <span class="font-black text-lg" style="color: {{ $primary }};">
                                        Score {{ $candidat->score_matching }}/100
                                    </span>
                                </div>

                                {{-- Avatar et nom --}}
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg flex-shrink-0"
                                         style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="" class="w-full h-full object-cover rounded-xl">
                                        @else
                                            {{ $initiale }}
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-black text-gray-900">
                                            {{ $candidat->user->name ?? 'Prestataire' }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $candidat->main_profession ?? $candidat->profession ?? 'Prestataire' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Barre de matching --}}
                                <div class="mb-4">
                                    <div class="match-bar">
                                        <div class="match-bar-fill" 
                                             style="width: {{ $candidat->score_matching }}%; 
                                                    background: linear-gradient(90deg, {{ $primary }}, {{ $secondary }});">
                                        </div>
                                    </div>
                                </div>

                                {{-- Détails --}}
                                <div class="space-y-3 pt-4 border-t border-gray-100">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Niveau</span>
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $candidat->niveau_libelle ?? ($candidat->niveau ?? 'Non défini') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Note</span>
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $candidat->average_rating ? number_format($candidat->average_rating, 1) . ' / 5' : 'N/A' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Missions</span>
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $candidat->missions_acceptees_count ?? 0 }} réalisée(s)
                                        </span>
                                    </div>
                                    
                                    @if(isset($candidat->distance_km))
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Distance</span>
                                            <span class="text-sm font-bold text-gray-900">
                                                {{ number_format($candidat->distance_km, 1) }} km
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Zone</span>
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $candidat->intervention_area ?? 'Non précisée' }}
                                        </span>
                                    </div>
                                </div>

                                @if($candidat->score_interne)
                                    <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                                        <span class="text-xs font-bold text-gray-500">
                                            Score interne: {{ $candidat->score_interne }}/100
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Barre d'action --}}
                    <div id="action-bar" class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 transition-all duration-300">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    <span id="selection-status">👆 Sélectionnez un prestataire pour attribuer la mission</span>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Une notification sera envoyée au prestataire choisi. Il aura 
                                    <strong style="color: {{ $primary }};">{{ config('artilo.delai_acceptation_minutes', 20) }} minutes</strong> pour répondre.
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.attributions.index') }}" 
                                   class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                    Annuler
                                </a>
                                
                                <button type="submit" id="btn-valider"
                                        class="btn-primary opacity-50 cursor-not-allowed"
                                        disabled>
                                    ✅ Valider l'attribution
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

            {{-- Légende des scores --}}
            <div class="mt-6 p-4 rounded-xl text-sm" style="background: color-mix(in srgb, {{ $primary }} 5%, #fff); color: {{ $muted }};">
                <p class="font-bold mb-1">💡 Comment est calculé le score ?</p>
                <p>
                    <strong>Niveau</strong> (60 pts max) · 
                    <strong>Évaluations</strong> (20 pts max) · 
                    <strong>Expérience</strong> (20 pts max) · 
                    <strong>Proximité</strong> (20 pts max)
                </p>
            </div>
        </div>
    </div>

    <script>
        function selectCandidate(card, candidateId) {
            document.querySelectorAll('.candidate-card').forEach(c => {
                c.classList.remove('selected');
            });
            
            card.classList.add('selected');
            document.getElementById('selected_artisan_id').value = candidateId;
            
            const btnValider = document.getElementById('btn-valider');
            btnValider.disabled = false;
            btnValider.classList.remove('opacity-50', 'cursor-not-allowed');
            
            const candidateName = card.querySelector('h3').textContent.trim();
            document.getElementById('selection-status').innerHTML = 
                '✅ Prestataire sélectionné : <strong style="color: {{ $primary }};">' + candidateName + '</strong>';
        }
    </script>
</x-app-layout>