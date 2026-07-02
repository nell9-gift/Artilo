{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_name'] ?? null }} – Trouvez le bon artisan</title>
    <link rel="icon" href="{{ asset($settings['favicon'] ?? null) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    {{-- Couleurs dynamiques depuis la DB --}}
    <style>
        :root {
            --c-purple      : {{ $settings['color_primary']         ?? null }};
            --c-purple-mid  : {{ $settings['color_primary_dark']    ?? null }};
            --c-purple-900  : {{ $settings['color_primary_900']     ?? null }};
            --c-purple-800  : {{ $settings['color_primary_800']     ?? null }};
            --c-purple-light: {{ $settings['color_primary_light']   ?? null }};
            --c-amber       : {{ $settings['color_secondary']       ?? null }};
            --c-amber-light : {{ $settings['color_secondary_light'] ?? null }};
            --c-bg          : {{ $settings['color_bg']              ?? null }};
            --c-bg-2        : {{ $settings['color_bg_2']            ?? null }};
            --c-text        : {{ $settings['color_text']            ?? null }};
            --c-muted       : {{ $settings['color_muted']           ?? null }};
        }
    </style>
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <nav id="navbar">
        <a href="/" class="nav-logo">
            <img src="{{ asset($settings['logo'] ?? null) }}"
                 alt="{{ $settings['site_name'] ?? null }}"
                 class="nav-logo-img">
            {{ $settings['site_name'] ?? null }}
        </a>
        <ul class="nav-links">
            <li><a href="/" class="active">Accueil</a></li>
            <li><a href="/about">À propos</a></li>
            <li><a href="/contact">Contacts</a></li>
        </ul>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-ghost"> se connecter</a>
            <a href="{{ route('register.artisan') }}" class="btn-orange">s inscrire</a>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="hero">
        <div class="hero-bg-wrapper">
            <div class="hero-bg" id="heroBg"></div>
            <div class="hero-bg-sweep" id="heroSweep"></div>
            <div class="hero-particles">
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
                <span class="particle"></span>
            </div>
        </div>

        <div class="hero-inner">
            <div class="hero-text">
                <div class="hero-eyebrow">Service à domicile au Togo</div>
                <h1 class="hero-title">
                    Trouvez un artisan de confiance <span class="accent">facilement</span>
                </h1>
                <p class="hero-subtitle">
                    Plombiers, électriciens, menuisiers, maçons et bien d'autres professionnels qualifiés, réunis sur une seule plateforme pour répondre rapidement à vos besoins.
                </p>
                <div class="hero-ctas">
                    <a href="{{ route('register') }}" class="cta-primary"> Faire une demande</a>
                    <a href="{{ route('register.artisan') }}" class="cta-secondary"> Devenir artisan</a>
                </div>
            </div>

            <div class="hero-artisans" id="artisansContainer">
                <div class="artisan-figure" data-index="1">
                    <img src="{{ asset($settings['hero_image_1'] ?? null) }}" alt="Plombier">
                </div>
                <div class="artisan-figure" data-index="2">
                    <img src="{{ asset($settings['hero_image_2'] ?? null) }}" alt="Électricien">
                </div>
                <div class="artisan-figure" data-index="3">
                    <img src="{{ asset($settings['hero_image_3'] ?? null) }}" alt="BTP">
                </div>
                <div class="artisan-figure" data-index="4">
                    <img src="{{ asset($settings['hero_image_4'] ?? null) }}" alt="Charpentier">
                </div>
            </div>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number" data-target="500">0<span>+</span></div>
                <div class="stat-label">Artisans qualifiés</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="3000">0<span>+</span></div>
                <div class="stat-label">Projets réalisés</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="98">0<span>%</span></div>
                <div class="stat-label">Clients satisfaits</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="100">0<span>%</span></div>
                <div class="stat-label">Sécurité garantie</div>
            </div>
        </div>

        <div class="hero-scroll-hint">
            <span>Découvrir</span>
            <div class="scroll-arrow"></div>
        </div>
    </section>


    {{-- ===== SECTION OUTILS FLOTTANTS ===== --}}
    <section class="tools-section" id="toolsSection">
        <div class="tools-bg-grid"></div>

        <div class="tools-floating-layer" id="toolsLayer">
            <div class="tool-item tool-hammer" data-speed="-0.3">
                <svg viewBox="0 0 80 80" fill="none"><rect x="30" y="8" width="18" height="32" rx="4" fill="#7C3AED" opacity=".9"/><rect x="8" y="8" width="26" height="14" rx="3" fill="#5B21B6"/><rect x="36" y="38" width="8" height="34" rx="3" fill="#4C1D95" opacity=".8"/></svg>
            </div>
            <div class="tool-item tool-wrench" data-speed="0.2">
                <svg viewBox="0 0 80 80" fill="none"><ellipse cx="20" cy="20" rx="14" ry="8" fill="#7C3AED" opacity=".85" transform="rotate(-45 20 20)"/><rect x="30" y="28" width="8" height="38" rx="4" fill="#5B21B6" transform="rotate(-45 30 28)"/></svg>
            </div>
            <div class="tool-item tool-drill" data-speed="-0.15">
                <svg viewBox="0 0 80 80" fill="none"><rect x="10" y="25" width="40" height="22" rx="6" fill="#7C3AED" opacity=".9"/><polygon points="50,30 70,36 50,42" fill="#4C1D95"/><rect x="18" y="15" width="10" height="14" rx="3" fill="#5B21B6"/><circle cx="22" cy="22" r="4" fill="#8B5CF6"/></svg>
            </div>
            <div class="tool-item tool-spanner" data-speed="0.25">
                <svg viewBox="0 0 80 80" fill="none"><path d="M20 12 C10 12 6 20 10 28 L56 62 C60 66 68 64 68 58 C68 52 60 50 56 46 L26 20 C22 16 20 12 20 12Z" fill="#7C3AED" opacity=".8"/><circle cx="18" cy="16" r="8" fill="none" stroke="#5B21B6" stroke-width="4"/><circle cx="66" cy="60" r="8" fill="none" stroke="#5B21B6" stroke-width="4"/></svg>
            </div>
            <div class="tool-item tool-helmet" data-speed="-0.2">
                <svg viewBox="0 0 80 80" fill="none"><path d="M10 50 Q10 20 40 18 Q70 20 70 50Z" fill="#F59E0B" opacity=".9"/><rect x="6" y="48" width="68" height="10" rx="5" fill="#D97706"/><rect x="18" y="50" width="44" height="6" rx="3" fill="#FCD34D" opacity=".6"/></svg>
            </div>
            <div class="tool-item tool-ruler" data-speed="0.1">
                <svg viewBox="0 0 80 80" fill="none"><rect x="4" y="30" width="72" height="20" rx="3" fill="#7C3AED" opacity=".7"/><line x1="14" y1="30" x2="14" y2="40" stroke="white" stroke-width="1.5"/><line x1="24" y1="30" x2="24" y2="44" stroke="white" stroke-width="1.5"/><line x1="34" y1="30" x2="34" y2="40" stroke="white" stroke-width="1.5"/><line x1="44" y1="30" x2="44" y2="44" stroke="white" stroke-width="1.5"/><line x1="54" y1="30" x2="54" y2="40" stroke="white" stroke-width="1.5"/><line x1="64" y1="30" x2="64" y2="44" stroke="white" stroke-width="1.5"/></svg>
            </div>
        </div>

        <div class="tools-content">
            <p class="section-eyebrow">La plateforme</p>
            <h2 class="tools-title">Tous les artisans qualifiés,<br><em>réunis sur une seule plateforme.</em></h2>
            <p class="tools-sub">Du plombier d'urgence au charpentier de confiance, Artilo rassemble les meilleurs professionnels du Togo. Vérifiés. Disponibles. Prêts à intervenir.</p>
            <a href="/artisans" class="cta-outline">En savoir plus →</a>
        </div>
    </section>


    {{-- ===== SECTION COMMENT ÇA MARCHE ===== --}}
    <section class="how-section" id="howSection">

        <div class="how-header">
            <p class="section-eyebrow">Le processus</p>
            <h2>Comment ça marche ?</h2>
        </div>

        <div class="pipes-bg" aria-hidden="true">
            <svg class="pipes-svg" viewBox="0 0 1200 1800" preserveAspectRatio="xMidYMid slice" fill="none">
                <path id="pipePath" d="M 600 0 L 600 200 Q 600 260 540 260 L 200 260 Q 140 260 140 320 L 140 500 Q 140 560 200 560 L 1000 560 Q 1060 560 1060 620 L 1060 800 Q 1060 860 1000 860 L 200 860 Q 140 860 140 920 L 140 1100 Q 140 1160 200 1160 L 1000 1160 Q 1060 1160 1060 1220 L 1060 1800" stroke="#3B0764" stroke-width="28" stroke-linecap="round"/>
                <path id="pipeGlow" d="M 600 0 L 600 200 Q 600 260 540 260 L 200 260 Q 140 260 140 320 L 140 500 Q 140 560 200 560 L 1000 560 Q 1060 560 1060 620 L 1060 800 Q 1060 860 1000 860 L 200 860 Q 140 860 140 920 L 140 1100 Q 140 1160 200 1160 L 1000 1160 Q 1060 1160 1060 1220 L 1060 1800" stroke="url(#pipeGradient)" stroke-width="6" stroke-linecap="round"/>
                <circle cx="600" cy="260" r="22" fill="#4C1D95" stroke="#7C3AED" stroke-width="4"/>
                <circle cx="140" cy="560" r="22" fill="#4C1D95" stroke="#7C3AED" stroke-width="4"/>
                <circle cx="1060" cy="860" r="22" fill="#4C1D95" stroke="#7C3AED" stroke-width="4"/>
                <circle cx="140" cy="1160" r="22" fill="#4C1D95" stroke="#7C3AED" stroke-width="4"/>
                <defs>
                    <linearGradient id="pipeGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#A78BFA" stop-opacity="0"/>
                        <stop offset="50%" stop-color="#A78BFA" stop-opacity="1"/>
                        <stop offset="100%" stop-color="#A78BFA" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
            <div class="pipe-fluid" id="pipeFluid"></div>
        </div>

        <div class="how-steps">
            <div class="how-step" data-step="1">
                <div class="step-content step-right">
                    <div class="step-number">01</div>
                    <h3>Décrivez votre besoin</h3>
                    <p>Remplissez une demande simple en quelques secondes. Type de travaux, localisation, urgence… on s'occupe du reste.</p>
                </div>
            </div>
            <div class="how-step" data-step="2">
                <div class="step-content step-left">
                    <div class="step-number">02</div>
                    <h3>Recevez des propositions</h3>
                    <p>Les artisans disponibles proches de vous reçoivent votre demande et vous envoient leurs propositions rapidement.</p>
                </div>
            </div>
            <div class="how-step" data-step="3">
                <div class="step-content step-right">
                    <div class="step-number">03</div>
                    <h3>Choisissez votre artisan</h3>
                    <p>Comparez les profils, les avis clients et les tarifs. Choisissez en toute confiance et confirmez la mission.</p>
                </div>
            </div>
            <div class="how-step" data-step="4">
                <div class="step-content step-left">
                    <div class="step-number">04</div>
                    <h3>Travail terminé, mission accomplie</h3>
                    <p>L'artisan intervient, vous validez le travail. Le paiement est débloqué uniquement si vous êtes satisfait.</p>
                </div>
            </div>
        </div>
    </section>


    {{-- ===== SECTION NOS MÉTIERS ===== --}}
    <section class="trades-section" id="tradesSection">

        <div class="trades-deco" aria-hidden="true">
            <div class="deco-ruler"></div>
            <div class="deco-level"></div>
            <div class="deco-square"></div>
            <div class="deco-tape"></div>
        </div>

        <div class="trades-header">
            <p class="section-eyebrow">Notre réseau</p>
            <h2 class="trades-title">Nos métiers</h2>
            <p class="trades-sub">Des spécialistes dans chaque domaine, disponibles près de chez vous.</p>
        </div>

        <div class="trades-carousel-wrapper">
            <button class="carousel-btn carousel-prev" id="carouselPrev" aria-label="Précédent">‹</button>

            <div class="trades-carousel" id="tradesCarousel">
                <div class="trade-card">
                    <div class="trade-image">
                        <img src="{{ asset($settings['trade_image_plomberie'] ?? null) }}" alt="Plomberie">
                    </div>
                    <h3>Plomberie</h3>
                    <p>Fuites, installations sanitaires, chauffe-eau, canalisations. Intervention rapide garantie.</p>
                </div>
                <div class="trade-card">
                    <div class="trade-image">
                        <img src="{{ asset($settings['trade_image_electricite'] ?? null) }}" alt="Électricité">
                    </div>
                    <h3>Électricité</h3>
                    <p>Tableaux électriques, prises, éclairage, câblage. Travaux aux normes, certifiés.</p>
                </div>
                <div class="trade-card">
                    <div class="trade-image">
                        <img src="{{ asset($settings['trade_image_maconnerie'] ?? null) }}" alt="Maçonnerie">
                    </div>
                    <h3>Maçonnerie</h3>
                    <p>Construction, rénovation, enduits, carrelage. Du gros œuvre à la finition.</p>
                </div>
                <div class="trade-card">
                    <div class="trade-image">
                        <img src="{{ asset($settings['trade_image_menuiserie'] ?? null) }}" alt="Menuiserie">
                    </div>
                    <h3>Menuiserie</h3>
                    <p>Portes, fenêtres, meubles sur mesure, parquet. Bois massif ou dérivés.</p>
                </div>
                <div class="trade-card">
                    <h3>Charpente</h3>
                    <p>Toitures, charpentes traditionnelles et industrielles, ossatures bois.</p>
                </div>
                <div class="trade-card">
                    <h3>Peinture</h3>
                    <p>Intérieur, extérieur, décorative, ravalement. Finitions impeccables.</p>
                </div>
                <div class="trade-card">
                    <h3>Climatisation</h3>
                    <p>Installation, entretien, réparation de systèmes de climatisation et ventilation.</p>
                </div>
                <div class="trade-card">
                    <h3>Serrurerie</h3>
                    <p>Dépannage, remplacement de serrures, blindage de portes, coffres-forts.</p>
                </div>
            </div>

            <button class="carousel-btn carousel-next" id="carouselNext" aria-label="Suivant">›</button>
        </div>

        <div class="carousel-dots" id="carouselDots"></div>

    </section>


    {{-- ===== SECTION POURQUOI ARTILO ===== --}}
    <section class="why-section" id="whySection">

        <div class="saw-deco" aria-hidden="true">
            <div class="saw-blade" id="sawBlade">
                <svg viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="90" fill="#1A0533" stroke="#3B0764" stroke-width="3"/>
                    <circle cx="100" cy="100" r="60" fill="#0F0120" stroke="#4C1D95" stroke-width="2"/>
                    <circle cx="100" cy="100" r="15" fill="#7C3AED"/>
                    <g id="sawTeeth">
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(30 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(60 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(90 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(120 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(150 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(180 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(210 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(240 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(270 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(300 100 100)"/>
                        <polygon points="100,10 108,28 92,28" fill="#5B21B6" transform="rotate(330 100 100)"/>
                    </g>
                    <line x1="100" y1="40" x2="100" y2="160" stroke="#3B0764" stroke-width="2"/>
                    <line x1="40" y1="100" x2="160" y2="100" stroke="#3B0764" stroke-width="2"/>
                    <line x1="57" y1="57" x2="143" y2="143" stroke="#3B0764" stroke-width="2"/>
                    <line x1="143" y1="57" x2="57" y2="143" stroke="#3B0764" stroke-width="2"/>
                </svg>
            </div>
            <div class="saw-sparks" id="sawSparks">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>

        <div class="why-header">
            <p class="section-eyebrow">Nos engagements</p>
            <h2>Pourquoi choisir Artilo ?</h2>
        </div>

        <div class="why-cards">
            <div class="why-card" data-why="1">
                <div class="why-card-glow"></div>
                <div class="why-icon">
                    <svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" fill="#7C3AED" opacity=".15"/><path d="M24 10 L28 20 L40 20 L30 28 L34 40 L24 32 L14 40 L18 28 L8 20 L20 20Z" fill="#7C3AED"/><path d="M20 22 L24 32 L28 22" fill="#A78BFA"/></svg>
                </div>
                <h3>Artisans vérifiés</h3>
                <p>Chaque professionnel passe par une vérification d'identité, de compétences et d'expérience avant d'intégrer notre réseau. Votre sécurité est notre priorité.</p>
                <div class="why-badge">✓ Certifié Artilo</div>
            </div>
            <div class="why-card" data-why="2">
                <div class="why-card-glow"></div>
                <div class="why-icon">
                    <svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" fill="#7C3AED" opacity=".15"/><rect x="10" y="16" width="28" height="20" rx="4" fill="#7C3AED"/><rect x="14" y="28" width="8" height="4" rx="1" fill="#A78BFA"/><rect x="26" y="28" width="8" height="4" rx="1" fill="#A78BFA"/><path d="M17 16 L17 12 Q17 8 24 8 Q31 8 31 12 L31 16" stroke="#A78BFA" stroke-width="2.5" fill="none"/></svg>
                </div>
                <h3>Paiement sécurisé</h3>
                <p>Effectuez vos paiements en toute confiance grâce aux solutions Mobile Money. Toutes les transactions sont sécurisées afin de garantir la confidentialité de vos informations et la protection de vos paiements.</p>
                <div class="why-badge">Protégé</div>
            </div>
            <div class="why-card" data-why="3">
                <div class="why-card-glow"></div>
                <div class="why-icon">
                    <svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="20" fill="#7C3AED" opacity=".15"/><path d="M24 8 C16 8 10 14 10 22 C10 30 16 36 24 38 C32 36 38 30 38 22 C38 14 32 8 24 8Z" fill="#7C3AED" opacity=".6"/><circle cx="24" cy="20" r="5" fill="#A78BFA"/><path d="M14 36 Q14 28 24 26 Q34 28 34 36" fill="#A78BFA"/></svg>
                </div>
                <h3>Assistance 24/7</h3>
                <p>Notre équipe de support est disponible à toute heure. Une urgence à 3h du matin ? Nous avons des artisans d'astreinte et un service client réactif pour vous aider.</p>
                <div class="why-badge">Toujours là</div>
            </div>
        </div>
    </section>


    {{-- ===== SECTION ZONE DE COUVERTURE ===== --}}
    <section class="coverage-section" id="coverageSection">
        <div class="coverage-inner">
            <div class="coverage-text">
                <p class="section-eyebrow">Notre territoire</p>
                <h2>Présents partout<br>au Togo</h2>
                <p class="coverage-sub">Du Grand Lomé jusqu'à Dapaong, notre réseau d'artisans s'étend progressivement sur l'ensemble du territoire togolais.</p>

                <div class="coverage-cities">
                    <div class="city-tag active" data-city="lome"> Lomé</div>
                    <div class="city-tag" data-city="tsevie"> Tsévié</div>
                    <div class="city-tag" data-city="atakpame"> Atakpamé</div>
                    <div class="city-tag" data-city="sokode"> Sokodé</div>
                    <div class="city-tag" data-city="kara"> Kara</div>
                    <div class="city-tag" data-city="dapaong"> Dapaong</div>
                </div>

                <div class="coverage-stats">
                    <div class="cov-stat">
                        <span class="cov-number" data-target="120">0</span>
                        <span class="cov-label">Communes</span>
                    </div>
                    <div class="cov-stat">
                        <span class="cov-number" data-target="500">0</span>
                        <span class="cov-label">Artisans</span>
                    </div>
                    <div class="cov-stat">
                        <span class="cov-number" data-target="3000">0</span>
                        <span class="cov-label">Missions</span>
                    </div>
                </div>
            </div>
            <div>
                <img src="{{ asset($settings['togo_map_image'] ?? null) }}">
            </div>
        </div>
    </section>


    {{-- ===== SECTION AVIS CLIENTS ===== --}}
    <section class="reviews-section" id="reviewsSection">
        <div class="reviews-header">
            <p class="section-eyebrow">Ils nous font confiance</p>
            <h2>Ce que disent nos clients</h2>
        </div>

        <div class="reviews-track-wrapper">
            <div class="reviews-track" id="reviewsTrack">
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Mon plombier est arrivé en moins de 45 minutes après ma demande. Travail propre et rapide. Je recommande vivement Artilo !"</p>
                    <div class="review-author">
                        <div class="author-avatar">KA</div>
                        <div><strong>Kofi Amevor</strong><span>Lomé · Plomberie</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Excellent service. L'électricien était très professionnel et a bien expliqué ce qu'il faisait. Le paiement sécurisé m'a rassuré."</p>
                    <div class="review-author">
                        <div class="author-avatar">AD</div>
                        <div><strong>Akossiwa Dossou</strong><span>Tsévié · Électricité</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Artilo a transformé ma façon de trouver des artisans. Plus besoin de chercher dans le quartier, tout est là, simple et fiable."</p>
                    <div class="review-author">
                        <div class="author-avatar">YT</div>
                        <div><strong>Yves Tossou</strong><span>Atakpamé · Maçonnerie</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"J'ai eu trois devis en moins d'une heure. J'ai choisi le meilleur rapport qualité-prix. Le menuisier a fait un travail remarquable."</p>
                    <div class="review-author">
                        <div class="author-avatar">FB</div>
                        <div><strong>Fati Balao</strong><span>Sokodé · Menuiserie</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Très bonne expérience. L'application est intuitive, le support est réactif et les artisans sont vraiment qualifiés. Continuez comme ça !"</p>
                    <div class="review-author">
                        <div class="author-avatar">MP</div>
                        <div><strong>Marc Peki</strong><span>Kara · Peinture</span></div>
                    </div>
                </div>
                {{-- Dupliqués pour loop infini --}}
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Mon plombier est arrivé en moins de 45 minutes après ma demande. Travail propre et rapide. Je recommande vivement Artilo !"</p>
                    <div class="review-author">
                        <div class="author-avatar">KA</div>
                        <div><strong>Kofi Amevor</strong><span>Lomé · Plomberie</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Excellent service. L'électricien était très professionnel et a bien expliqué ce qu'il faisait. Le paiement sécurisé m'a rassuré."</p>
                    <div class="review-author">
                        <div class="author-avatar">AD</div>
                        <div><strong>Akossiwa Dossou</strong><span>Tsévié · Électricité</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">⭐⭐⭐⭐⭐</div>
                    <p class="review-text">"Artilo a transformé ma façon de trouver des artisans. Plus besoin de chercher dans le quartier, tout est là, simple et fiable."</p>
                    <div class="review-author">
                        <div class="author-avatar">YT</div>
                        <div><strong>Yves Tossou</strong><span>Atakpamé · Maçonnerie</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ===== CTA FINAL ===== --}}
    <section class="cta-section" id="ctaSection">
        <div class="cta-bg-anim">
            <div class="cta-orb cta-orb-1"></div>
            <div class="cta-orb cta-orb-2"></div>
            <div class="cta-orb cta-orb-3"></div>
        </div>
        <div class="cta-content">
            <p class="section-eyebrow" style="color: #C4B5FD;">Commencez maintenant</p>
            <h2>Prêt à trouver<br>votre artisan ?</h2>
            <p>Rejoignez des milliers de Togolais qui font confiance à Artilo chaque jour.</p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="cta-primary">Faire une demande</a>
                <a href="{{ route('register.artisan') }}" class="cta-primary cta-outline-white"> Devenir artisan</a>
            </div>
        </div>
    </section>


    {{-- ===== FOOTER ===== --}}
    <footer class="footer">
        <div class="footer-inner">

            <div class="footer-brand">
                <a href="/" class="footer-logo">{{ $settings['site_name'] ?? null }}</a>
                <p>La plateforme qui connecte les particuliers aux meilleurs artisans du Togo. Simple, rapide, sécurisé.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="Twitter/X">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/about">À propos</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/blog">Blog</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="/metiers/plomberie">Plomberie</a></li>
                    <li><a href="/metiers/electricite">Électricité</a></li>
                    <li><a href="/metiers/menuiserie">Menuiserie</a></li>
                    <li><a href="/metiers/maconnerie">Maçonnerie</a></li>
                    <li><a href="/metiers">Tous les métiers →</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li>{{ $settings['contact_phone'] ?? null }}</li>
                    <li>{{ $settings['contact_email'] ?? null }}</li>
                    <li>{{ $settings['contact_city']  ?? null }}</li>
                    <li>Lun–Sam, 8h–18h</li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>© 2026 {{ $settings['site_name'] ?? null }} · Tous droits réservés.</span>
            <div class="footer-legal">
                <a href="/mentions-legales">Mentions légales</a>
                <a href="/confidentialite">Confidentialité</a>
                <a href="/cgv">CGV</a>
            </div>
        </div>
    </footer>

    {{-- GSAP --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/welcome.js') }}"></script>

</body>
</html>