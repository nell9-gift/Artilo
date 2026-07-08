@php
    $user = Auth::user();
    $profileScore = 0;
    $profileChecks = [
        'Metier' => filled($artisan?->profession),
        'Zone' => filled($artisan?->intervention_area),
        'Adresse' => filled($artisan?->address),
        'Description' => filled($artisan?->description),
        'Photos' => ! empty($artisan?->photos),
        'Document' => filled($artisan?->identity_document),
    ];

    foreach ($profileChecks as $isReady) {
        $profileScore += $isReady ? 1 : 0;
    }

    $profilePercent = count($profileChecks) > 0 ? round(($profileScore / count($profileChecks)) * 100) : 0;
    $status = $artisan?->status ?? 'pending';
    $statusLabels = [
        'pending' => 'En attente',
        'approved' => 'Valide',
        'rejected' => 'Refuse',
    ];

    $stats = [
        ['label' => 'Demandes recues', 'value' => 0, 'hint' => 'missions a connecter'],
        ['label' => 'Missions actives', 'value' => 0, 'hint' => 'travaux en cours'],
        ['label' => 'Revenus a reverser', 'value' => '0 F', 'hint' => 'apres commission Artilo'],
        ['label' => 'Note moyenne', 'value' => $artisan?->average_rating ?? 'N/A', 'hint' => 'avis clients'],
    ];

    $quickActions = [
        ['label' => 'Modifier mon profil', 'href' => route('artisan.profil.edit'), 'tone' => 'primary'],
        ['label' => 'Voir mes demandes', 'href' => '#missions', 'tone' => 'secondary'],
        ['label' => 'Paiements', 'href' => '#paiements', 'tone' => 'secondary'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('MaPage Artisan') }}
        </h2>
    </x-slot>

    <div class="artisan-page">
        <style>
            .artisan-page {
                --artisan-primary: #14532d;
                --artisan-accent: #d97706;
                --artisan-ink: #17211a;
                --artisan-muted: #647067;
                background: linear-gradient(135deg, #f3fbf5 0%, #fff 48%, #fff7ed 100%);
                color: var(--artisan-ink);
                min-height: calc(100vh - 4rem);
                padding: 2rem 1rem 3rem;
            }

            .artisan-shell {
                max-width: 1180px;
                margin: 0 auto;
            }

            .artisan-hero,
            .artisan-card {
                background: rgba(255, 255, 255, .94);
                border: 1px solid rgba(20, 83, 45, .12);
                border-radius: .5rem;
                box-shadow: 0 24px 60px -48px rgba(20, 83, 45, .65);
            }

            .artisan-hero {
                display: grid;
                grid-template-columns: minmax(0, 1.4fr) minmax(280px, .6fr);
                overflow: hidden;
            }

            .artisan-hero-main {
                padding: 2rem;
            }

            .artisan-hero-side {
                background:
                    linear-gradient(145deg, rgba(20, 83, 45, .9), rgba(21, 128, 61, .82)),
                    url('{{ asset('images/artisan_btp.png') }}');
                background-size: cover;
                background-position: center;
                color: #fff;
                padding: 2rem;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                min-height: 270px;
            }

            .artisan-eyebrow {
                color: var(--artisan-accent);
                font-size: .78rem;
                font-weight: 900;
                letter-spacing: .16em;
                text-transform: uppercase;
            }

            .artisan-title {
                margin-top: .65rem;
                font-size: clamp(2rem, 4vw, 3.3rem);
                line-height: 1;
                font-weight: 950;
            }

            .artisan-text {
                color: var(--artisan-muted);
                line-height: 1.7;
            }

            .artisan-actions,
            .artisan-grid,
            .artisan-two-col {
                display: grid;
                gap: 1rem;
            }

            .artisan-actions {
                grid-template-columns: repeat(auto-fit, minmax(180px, max-content));
                margin-top: 1.5rem;
            }

            .artisan-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .5rem;
                padding: .8rem 1rem;
                font-weight: 900;
                text-decoration: none;
                color: var(--artisan-primary);
                background: #ecfdf3;
            }

            .artisan-btn.primary {
                color: #fff;
                background: linear-gradient(135deg, var(--artisan-primary), #15803d);
            }

            .artisan-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                margin-top: 1rem;
            }

            .artisan-two-col {
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
                margin-top: 1rem;
            }

            .artisan-card {
                padding: 1.35rem;
            }

            .artisan-card h3 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 950;
            }

            .artisan-stat {
                margin-top: .7rem;
                font-size: 2rem;
                font-weight: 950;
            }

            .artisan-status {
                display: inline-flex;
                border-radius: 999px;
                padding: .28rem .7rem;
                font-size: .78rem;
                font-weight: 900;
                color: var(--artisan-primary);
                background: #dcfce7;
            }

            .artisan-progress {
                height: .55rem;
                overflow: hidden;
                border-radius: 999px;
                background: #ecfdf3;
            }

            .artisan-progress span {
                display: block;
                height: 100%;
                width: var(--value);
                background: linear-gradient(90deg, var(--artisan-primary), var(--artisan-accent));
            }

            .artisan-list {
                margin-top: 1rem;
                display: grid;
                gap: .75rem;
            }

            .artisan-row {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                border-radius: .5rem;
                background: #fff;
                padding: .9rem 1rem;
            }

            .artisan-row strong {
                display: block;
            }

            .artisan-empty {
                border: 1px dashed rgba(20, 83, 45, .22);
                border-radius: .5rem;
                padding: 1rem;
                color: var(--artisan-muted);
                background: rgba(255, 255, 255, .7);
            }

            @media (max-width: 900px) {
                .artisan-hero,
                .artisan-two-col {
                    grid-template-columns: 1fr;
                }

                .artisan-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 560px) {
                .artisan-grid {
                    grid-template-columns: 1fr;
                }

                .artisan-row {
                    flex-direction: column;
                }
            }
        </style>

        <div class="artisan-shell">
            <section class="artisan-hero">
                <div class="artisan-hero-main">
                    <p class="artisan-eyebrow">Espace artisan</p>
                    <h1 class="artisan-title">Bonjour {{ $user->name }}</h1>
                    <p class="artisan-text mt-4">
                        Suivez vos demandes, votre profil public, vos paiements Mobile Money et les prochaines actions a traiter sur Artilo.
                    </p>
                    <div class="artisan-actions">
                        @foreach ($quickActions as $action)
                            <a class="artisan-btn {{ $action['tone'] === 'primary' ? 'primary' : '' }}" href="{{ $action['href'] }}">
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="artisan-hero-side">
                    <span class="artisan-status">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                    <h2 class="mt-4 text-2xl font-black">{{ ucfirst($artisan?->profession ?? 'Metier a renseigner') }}</h2>
                    <p class="mt-2 text-white/80">{{ $artisan?->intervention_area ?? 'Zone a renseigner' }}</p>
                </div>
            </section>

            <section class="artisan-grid">
                @foreach ($stats as $stat)
                    <article class="artisan-card">
                        <h3>{{ $stat['label'] }}</h3>
                        <p class="artisan-stat">{{ $stat['value'] }}</p>
                        <p class="artisan-text text-sm">{{ $stat['hint'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="artisan-two-col">
                <article class="artisan-card">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="artisan-eyebrow">Profil public</p>
                            <h3>Completion du profil</h3>
                        </div>
                        <span class="artisan-status">{{ $profilePercent }}%</span>
                    </div>
                    <div class="artisan-progress mt-4" style="--value: {{ $profilePercent }}%">
                        <span></span>
                    </div>
                    <div class="artisan-list">
                        @foreach ($profileChecks as $label => $isReady)
                            <div class="artisan-row">
                                <strong>{{ $label }}</strong>
                                <span class="artisan-status">{{ $isReady ? 'OK' : 'A completer' }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="artisan-card" id="paiements">
                    <p class="artisan-eyebrow">Paiements</p>
                    <h3>Acompte, solde et reversements</h3>
                    <div class="artisan-list">
                        <div class="artisan-row">
                            <div>
                                <strong>Commission Artilo</strong>
                                <span class="artisan-text text-sm">7% sur acompte et 7% sur solde</span>
                            </div>
                            <span class="artisan-status">Pret</span>
                        </div>
                        <div class="artisan-row">
                            <div>
                                <strong>Mobile Money</strong>
                                <span class="artisan-text text-sm">Moov ou Yas pour recevoir les 93%</span>
                            </div>
                            <span class="artisan-status">A connecter</span>
                        </div>
                        <div class="artisan-row">
                            <div>
                                <strong>Litiges</strong>
                                <span class="artisan-text text-sm">Decision finale par l'administration</span>
                            </div>
                            <span class="artisan-status">Admin</span>
                        </div>
                    </div>
                </article>
            </section>

            <section class="artisan-two-col" id="missions">
                <article class="artisan-card">
                    <p class="artisan-eyebrow">Missions</p>
                    <h3>Demandes recentes</h3>
                    <div class="artisan-list">
                        <div class="artisan-empty">
                            Les demandes clients apparaitront ici des que le module missions sera connecte.
                        </div>
                    </div>
                </article>

                <article class="artisan-card">
                    <p class="artisan-eyebrow">Agenda</p>
                    <h3>Planning d'intervention</h3>
                    <div class="artisan-list">
                        <div class="artisan-empty">
                            Les rendez-vous, diagnostics et interventions seront affiches ici.
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
