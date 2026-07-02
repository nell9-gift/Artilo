<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment ça marche - Artilo</title>
    <link rel="stylesheet" href="{{ asset('css/indexartisant.css') }}">
</head>
<body>

<header class="hero">
    <div class="container">
        <h1 class="title">Comment fonctionne Artilo</h1>
        <p class="subtitle">Artilo met en relation particuliers et artisans qualifiés, avec un paiement simple et sécurisé via mobile money.</p>
    </div>
</header>

<main class="container">

    <!-- 1. PRESENTATION GENERALE -->
    <section class="card">
        <h2>À propos d'Artilo</h2>
        <p>Artilo est une plateforme qui simplifie la mise en relation entre des particuliers ayant besoin de travaux et des artisans qualifiés (plomberie, électricité, menuiserie, maçonnerie). L'objectif est de rendre la recherche d'un artisan fiable plus rapide, plus transparente, et de sécuriser le paiement des prestations.</p>
    </section>

    <!-- 2. COMMENT CA MARCHE - ONGLET -->
    <section class="card">
        <h2>Comment ça marche</h2>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('particulier')">Pour les particuliers</button>
            <button class="tab-btn" onclick="switchTab('artisan')">Pour les artisans</button>
        </div>

        <div id="tab-particulier" class="tab-content active">
            <ol class="steps">
                <li>
                    <span class="step-number">1</span>
                    <div>
                        <h3>Décrire le besoin</h3>
                        <p>Le particulier indique le type de service recherché, une description du problème et sa localisation.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">2</span>
                    <div>
                        <h3>Mise en relation</h3>
                        <p>Artilo identifie les artisans disponibles correspondant au besoin et à la zone géographique indiquée.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">3</span>
                    <div>
                        <h3>Intervention</h3>
                        <p>L'artisan se déplace, évalue les travaux sur place et réalise la prestation convenue.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">4</span>
                    <div>
                        <h3>Paiement sécurisé</h3>
                        <p>Le règlement s'effectue via mobile money directement sur la plateforme, une fois la prestation validée.</p>
                    </div>
                </li>
            </ol>
        </div>

        <div id="tab-artisan" class="tab-content">
            <ol class="steps">
                <li>
                    <span class="step-number">1</span>
                    <div>
                        <h3>Inscription et vérification</h3>
                        <p>L'artisan crée un profil détaillant ses spécialités et son expérience, soumis à une vérification d'identité avant validation.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">2</span>
                    <div>
                        <h3>Réception des demandes</h3>
                        <p>L'artisan reçoit les demandes correspondant à ses compétences et à sa zone d'intervention.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">3</span>
                    <div>
                        <h3>Réalisation de la prestation</h3>
                        <p>L'artisan intervient chez le particulier et effectue les travaux convenus dans les délais annoncés.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number">4</span>
                    <div>
                        <h3>Réception du paiement</h3>
                        <p>Une fois la prestation confirmée par le client, le paiement est transféré à l'artisan via mobile money.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <!-- 3. PAIEMENT MOBILE MONEY -->
    <section class="card">
        <h2>Paiement par mobile money</h2>
        <p class="card-desc">Tous les paiements sur Artilo passent par mobile money, pour plus de sécurité et de traçabilité, sans manipulation d'espèces.</p>

        <div class="payment-grid">
            <div class="payment-item">
                <h3>Moyens acceptés</h3>
                <p>Les paiements peuvent être effectués via les principaux opérateurs de mobile money disponibles au Togo, notamment Flooz et T-Money.</p>
            </div>
            <div class="payment-item">
                <h3>Sécurité des transactions</h3>
                <p>Chaque paiement est associé à une demande précise et n'est débloqué qu'après confirmation de la prestation par le particulier.</p>
            </div>
            <div class="payment-item">
                <h3>Suivi des paiements</h3>
                <p>Un historique des transactions est conservé, permettant à chaque utilisateur de suivre ses paiements et encaissements.</p>
            </div>
        </div>
    </section>

    <!-- 4. ENGAGEMENTS -->
    <section class="card">
        <h2>Nos engagements</h2>
        <ul class="engagement-list">
            <li>Vérification de l'identité des artisans inscrits sur la plateforme.</li>
            <li>Transparence sur les modalités d'intervention avant le début des travaux.</li>
            <li>Paiement sécurisé, débloqué uniquement après validation de la prestation.</li>
            <li>Accompagnement progressif des utilisateurs sur l'ensemble du territoire couvert.</li>
        </ul>
    </section>

    <!-- 5. STATUT PLATEFORME -->
    <section class="card status">
        <h2>Statut du réseau Artilo</h2>
        <div class="status-grid">
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>Artisans vérifiés : en cours de validation</span>
            </div>
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>Zones couvertes : extension active</span>
            </div>
            
        </div>
    </section>

</main>

<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <h3 class="footer-logo">Artilo</h3>
            <p>Mettre en relation particuliers et artisans qualifiés, avec un paiement simple et sécurisé.</p>
        </div>

        <div class="footer-col">
            <h4>Navigation</h4>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">Comment ça marche</a></li>
                <li><a href="#">Devenir artisan</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Services</h4>
            <ul>
                <li>Plomberie</li>
                <li>Électricité</li>
                <li>Menuiserie</li>
                <li>Maçonnerieet autres ....</li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contact</h4>
            <ul>
                <li>Lomé, Togo</li>
                <li>contact@artilo.tg</li>
                <li>+228 90 00 00 00</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <span id="year"></span> Artilo. Tous droits réservés.</p>
    </div>
</footer>

<script src="{{ asset('js/indexartisans.js') }}"></script>
</body>
</html>