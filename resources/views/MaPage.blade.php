@php
    $user = Auth::user();
    $stats = [
        ['label' => 'Demandes envoyees', 'value' => 0, 'hint' => 'missions a connecter'],
        ['label' => 'Missions actives', 'value' => 0, 'hint' => 'artisan en cours'],
        ['label' => 'Paiements', 'value' => '0 F', 'hint' => 'acomptes et soldes'],
        ['label' => 'Avis donnes', 'value' => 0, 'hint' => 'retours apres mission'],
    ];

    $serviceCards = [
        ['title' => 'Plomberie', 'text' => 'Fuite, robinet, sanitaire, installation.'],
        ['title' => 'Electricite', 'text' => 'Panne, prise, tableau, eclairage.'],
        ['title' => 'Menuiserie', 'text' => 'Meuble, porte, reparation, pose.'],
        ['title' => 'Maconnerie', 'text' => 'Mur, carrelage, renovation, finition.'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('MaPage Particulier') }}
        </h2>
    </x-slot>

    <div class="customer-page">
        <style>
            .customer-page {
                --customer-primary: #4c1d95;
                --customer-accent: #0f766e;
                --customer-ink: #221832;
                --customer-muted: #6f647c;
                min-height: calc(100vh - 4rem);
                padding: 2rem 1rem 3rem;
                background: linear-gradient(135deg, #faf7ff 0%, #fff 52%, #eefcf9 100%);
                color: var(--customer-ink);
            }

            .customer-shell {
                max-width: 1180px;
                margin: 0 auto;
            }

            .customer-hero,
            .customer-card {
                border: 1px solid rgba(76, 29, 149, .12);
                border-radius: .5rem;
                background: rgba(255, 255, 255, .94);
                box-shadow: 0 24px 60px -48px rgba(76, 29, 149, .7);
            }

            .customer-hero {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
                overflow: hidden;
            }

            .customer-hero-main {
                padding: 2rem;
            }

            .customer-hero-media {
                min-height: 300px;
                background:
                    linear-gradient(145deg, rgba(76, 29, 149, .82), rgba(15, 118, 110, .68)),
                    url('{{ asset('images/artisan_plombier.png') }}');
                background-size: cover;
                background-position: center;
                display: flex;
                align-items: flex-end;
                padding: 2rem;
                color: #fff;
            }

            .customer-eyebrow {
                color: var(--customer-accent);
                font-size: .78rem;
                font-weight: 900;
                letter-spacing: .16em;
                text-transform: uppercase;
            }

            .customer-title {
                margin-top: .65rem;
                font-size: clamp(2rem, 4vw, 3.3rem);
                line-height: 1;
                font-weight: 950;
            }

            .customer-text {
                color: var(--customer-muted);
                line-height: 1.7;
            }

            .customer-actions,
            .customer-grid,
            .customer-two-col {
                display: grid;
                gap: 1rem;
            }

            .customer-actions {
                grid-template-columns: repeat(auto-fit, minmax(180px, max-content));
                margin-top: 1.5rem;
            }

            .customer-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .5rem;
                padding: .8rem 1rem;
                font-weight: 900;
                text-decoration: none;
                color: var(--customer-primary);
                background: #f3e8ff;
            }

            .customer-btn.primary {
                color: #fff;
                background: linear-gradient(135deg, var(--customer-primary), var(--customer-accent));
            }

            .customer-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                margin-top: 1rem;
            }

            .customer-two-col {
                grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
                margin-top: 1rem;
            }

            .customer-card {
                padding: 1.35rem;
            }

            .customer-card h3 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 950;
            }

            .customer-stat {
                margin-top: .7rem;
                font-size: 2rem;
                font-weight: 950;
            }

            .customer-list {
                margin-top: 1rem;
                display: grid;
                gap: .75rem;
            }

            .customer-row {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                border-radius: .5rem;
                background: #fff;
                padding: .9rem 1rem;
            }

            .customer-row strong {
                display: block;
            }

            .customer-status {
                display: inline-flex;
                border-radius: 999px;
                padding: .28rem .7rem;
                font-size: .78rem;
                font-weight: 900;
                color: var(--customer-primary);
                background: #ede9fe;
                white-space: nowrap;
            }

            .customer-service-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .8rem;
                margin-top: 1rem;
            }

            .customer-service {
                border-radius: .5rem;
                background: #fff;
                padding: 1rem;
            }

            .customer-empty {
                border: 1px dashed rgba(76, 29, 149, .22);
                border-radius: .5rem;
                padding: 1rem;
                color: var(--customer-muted);
                background: rgba(255, 255, 255, .7);
            }

            @media (max-width: 900px) {
                .customer-hero,
                .customer-two-col {
                    grid-template-columns: 1fr;
                }

                .customer-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 560px) {
                .customer-grid,
                .customer-service-grid {
                    grid-template-columns: 1fr;
                }

                .customer-row {
                    flex-direction: column;
                }
            }
        </style>

        <div class="customer-shell">
            <section class="customer-hero">
                <div class="customer-hero-main">
                    <p class="customer-eyebrow">Espace particulier</p>
                    <h1 class="customer-title">Bonjour {{ $user->name }}</h1>
                    <p class="customer-text mt-4">
                        Lancez une demande, suivez les artisans proposes, gerez vos paiements Mobile Money et retrouvez tout l'historique de vos travaux.
                    </p>
                    <div class="customer-actions">
                        <a href="#nouvelle-demande" class="customer-btn primary">Nouvelle demande</a>
                        <a href="#missions" class="customer-btn">Mes missions</a>
                        <a href="#paiements" class="customer-btn">Paiements</a>
                    </div>
                </div>
                <div class="customer-hero-media">
                    <div>
                        <span class="customer-status">Assistance Artilo</span>
                        <h2 class="mt-4 text-2xl font-black">Un artisan fiable, un paiement suivi, une decision admin en cas de litige.</h2>
                    </div>
                </div>
            </section>

            <section class="customer-grid">
                @foreach ($stats as $stat)
                    <article class="customer-card">
                        <h3>{{ $stat['label'] }}</h3>
                        <p class="customer-stat">{{ $stat['value'] }}</p>
                        <p class="customer-text text-sm">{{ $stat['hint'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="customer-two-col" id="nouvelle-demande">
                <article class="customer-card">
                    <p class="customer-eyebrow">Demande rapide</p>
                    <h3>Choisir un besoin</h3>
                    <div class="customer-service-grid">
                        @foreach ($serviceCards as $service)
                            <button type="button" class="customer-service text-left">
                                <strong>{{ $service['title'] }}</strong>
                                <p class="customer-text mt-1 text-sm">{{ $service['text'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </article>

                <article class="customer-card" id="paiements">
                    <p class="customer-eyebrow">Paiement securise</p>
                    <h3>Acompte puis solde</h3>
                    <div class="customer-list">
                        <div class="customer-row">
                            <div>
                                <strong>Mobile Money</strong>
                                <span class="customer-text text-sm">Moov ou Yas vers le compte Artilo.</span>
                            </div>
                            <span class="customer-status">Pret</span>
                        </div>
                        <div class="customer-row">
                            <div>
                                <strong>Reversement artisan</strong>
                                <span class="customer-text text-sm">Artilo garde 7%, l'artisan recoit 93%.</span>
                            </div>
                            <span class="customer-status">Automatique</span>
                        </div>
                        <div class="customer-row">
                            <div>
                                <strong>Litige</strong>
                                <span class="customer-text text-sm">L'administration tranche avant remboursement ou reversement.</span>
                            </div>
                            <span class="customer-status">Admin</span>
                        </div>
                    </div>
                </article>
            </section>

            <section class="customer-two-col" id="missions">
                <article class="customer-card">
                    <p class="customer-eyebrow">Suivi</p>
                    <h3>Missions en cours</h3>
                    <div class="customer-list">
                        <div class="customer-empty">
                            Les demandes, diagnostics, devis, acomptes et validations apparaitront ici quand le module missions sera branche.
                        </div>
                    </div>
                </article>

                <article class="customer-card">
                    <p class="customer-eyebrow">Confiance</p>
                    <h3>Artisans et support</h3>
                    <div class="customer-list">
                        <div class="customer-row">
                            <div>
                                <strong>Favoris</strong>
                                <span class="customer-text text-sm">Retrouver les artisans deja apprecies.</span>
                            </div>
                            <span class="customer-status">A connecter</span>
                        </div>
                        <div class="customer-row">
                            <div>
                                <strong>Avis</strong>
                                <span class="customer-text text-sm">Noter la mission apres validation.</span>
                            </div>
                            <span class="customer-status">A connecter</span>
                        </div>
                        <div class="customer-row">
                            <div>
                                <strong>Support</strong>
                                <span class="customer-text text-sm">Contacter Artilo en cas de blocage.</span>
                            </div>
                            <span class="customer-status">Disponible</span>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
