
{{-- 
============================================================
  1. BLOC PHP : CONFIGURATION & PRÉPARATION DES DONNÉES
  Ce bloc s'exécute côté serveur avant l'affichage de la page.
  Il récupère les paramètres du site, les statistiques, 
  construit le menu de navigation et définit les icônes SVG.
============================================================
--}}
@php
    // ------------------------------------------------------------
    // 1.1 RÉCUPÉRATION DES PARAMÈTRES DU SITE
    // La variable $settings est passée depuis le contrôleur.
    // On utilise l'opérateur null coalescent '??' pour définir
    // des valeurs par défaut si les clés n'existent pas.
    // ------------------------------------------------------------
    $siteName = $settings['site_name'] ?? 'Artilo';  // Nom du site
    $logo = $settings['logo'] ?? null;               // Logo du site (chemin)
    // Image de fond (hero) : on prend hero_image_3, sinon hero_image_1, sinon null
    $heroImage = $settings['hero_image_3'] ?? ($settings['hero_image_1'] ?? null);
    
    // Couleurs personnalisables (avec valeurs par défaut)
    $primary = $settings['color_primary'] ?? '#7C3AED';              // Violet primaire
    $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';     // Violet foncé
    $primaryText = $settings['color_primary_900'] ?? '#2E1065';      // Texte sur fond primaire
    $secondary = $settings['color_secondary'] ?? '#D97706';          // Orange secondaire
    $bg = $settings['color_bg_2'] ?? '#FAF8FF';                     // Couleur de fond
    $muted = $settings['color_muted'] ?? '#6B5B95';                 // Couleur pour texte secondaire

    // Récupération de l'utilisateur administrateur connecté
    $adminUser = Auth::user();
    // Génération du chemin de sa photo de profil (si elle existe)
    $adminPhoto = $adminUser?->profile_photo ? asset('storage/' . $adminUser->profile_photo) : null;

    // ------------------------------------------------------------
    // 1.2 STATISTIQUES DU DASHBOARD
    // Tableau associatif contenant les indicateurs clés affichés
    // dans la vue. Certaines valeurs sont des placeholders (0 ou '0 F')
    // en attendant la connexion des modules correspondants.
    // ------------------------------------------------------------
    $stats = [
        ['label' => 'Artisans', 'value' => $totalArtisans, 'hint' => 'tous statuts confondus'],
        ['label' => 'Clients', 'value' => $customerCount, 'hint' => 'comptes particuliers'],
        ['label' => 'Missions du jour', 'value' => 0, 'hint' => 'module a connecter'],
        ['label' => 'Revenus', 'value' => '0 F', 'hint' => 'paiements a connecter'],
        ['label' => 'Commissions', 'value' => '0 F', 'hint' => 'regle finance future'],
        ['label' => 'Paiements', 'value' => 0, 'hint' => 'transactions futures'],
        ['label' => 'Avis', 'value' => 0, 'hint' => 'moderation future'],
        ['label' => 'Signalements', 'value' => 0, 'hint' => 'support future'],
    ];

    // ------------------------------------------------------------
    // 1.3 MENU DE NAVIGATION (barre latérale)
    // Structure arborescente : groupes (ex: Utilisateurs, IA, Validation...)
    // Chaque groupe a un id, un label, une icône, et une liste d'items.
    // Chaque item a un id (utilisé pour afficher la section correspondante)
    // et un label (texte affiché dans le menu).
    // ------------------------------------------------------------
    $menuGroups = [
        [
            'id' => 'users',
            'label' => 'Utilisateurs',
            'icon' => 'users',  // Correspond au nom de l'icône dans le tableau $icons
            'items' => [
                ['id' => 'clients', 'label' => 'Clients'],
                ['id' => 'artisans', 'label' => 'Artisans'],
                ['id' => 'administrateurs', 'label' => 'Administrateurs'],
                ['id' => 'comptes-suspendus', 'label' => 'Comptes suspendus'],
            ],
        ],
        [
            'id' => 'ai',
            'label' => 'IA',
            'icon' => 'sparkles',
            'items' => [
                ['id' => 'ia-agent', 'label' => 'Agent IA'],
                ['id' => 'ia-affectation', 'label' => 'Aide affectation'],
                ['id' => 'ia-estimations', 'label' => 'Estimations'],
                ['id' => 'ia-alertes', 'label' => 'Alertes intelligentes'],
            ],
        ],
        [
            'id' => 'validation',
            'label' => 'Validation artisans',
            'icon' => 'shield',
            'items' => [
                ['id' => 'validation-attente', 'label' => 'En attente'],
                ['id' => 'validation-valides', 'label' => 'Valides'],
                ['id' => 'validation-refuses', 'label' => 'Refuses'],
                ['id' => 'documents-identite', 'label' => 'Documents identite'],
                ['id' => 'contrats-signes', 'label' => 'Contrats signes'],
            ],
        ],
        [
            'id' => 'platform',
            'label' => 'Metiers et zones',
            'icon' => 'tool',
            'items' => [
                ['id' => 'metiers', 'label' => 'Metiers'],
                ['id' => 'zones', 'label' => 'Zones intervention'],
                ['id' => 'villes', 'label' => 'Villes'],
                ['id' => 'quartiers', 'label' => 'Quartiers'],
                ['id' => 'regions', 'label' => 'Regions'],
            ],
        ],
        [
            'id' => 'missions',
            'label' => 'Missions',
            'icon' => 'clipboard',
            'items' => [
                ['id' => 'missions-toutes', 'label' => 'Toutes les missions'],
                ['id' => 'missions-attente', 'label' => 'En attente'],
                ['id' => 'missions-affectees', 'label' => 'Affectees'],
                ['id' => 'missions-acceptees', 'label' => 'Acceptees'],
                ['id' => 'missions-refusees', 'label' => 'Refusees'],
                ['id' => 'missions-diagnostic', 'label' => 'Diagnostic'],
                ['id' => 'devis-envoye', 'label' => 'Devis envoye'],
                ['id' => 'missions-acompte', 'label' => 'Attente acompte'],
                ['id' => 'missions-cours', 'label' => 'En cours'],
                ['id' => 'missions-terminees', 'label' => 'Terminees'],
                ['id' => 'missions-annulees', 'label' => 'Annulees'],
            ],
        ],
        [
            'id' => 'assignment',
            'label' => 'Affectation',
            'icon' => 'bolt',
            'items' => [
                ['id' => 'affectation-auto', 'label' => 'Automatique'],
                ['id' => 'affectation-manuelle', 'label' => 'Manuelle'],
                ['id' => 'artisans-disponibles', 'label' => 'Artisans disponibles'],
                ['id' => 'distances', 'label' => 'Distances'],
                ['id' => 'temps-restant', 'label' => 'Temps restant'],
            ],
        ],
        [
            'id' => 'finance',
            'label' => 'Finance',
            'icon' => 'card',
            'items' => [
                ['id' => 'paiements', 'label' => 'Paiements'],
                ['id' => 'acomptes', 'label' => 'Acomptes'],
                ['id' => 'soldes', 'label' => 'Soldes'],
                ['id' => 'remboursements', 'label' => 'Remboursements'],
                ['id' => 'paiements-echoues', 'label' => 'Paiements echoues'],
                ['id' => 'commissions', 'label' => 'Commissions'],
                ['id' => 'devis', 'label' => 'Devis'],
            ],
        ],
        [
            'id' => 'support',
            'label' => 'Qualite et support',
            'icon' => 'message',
            'items' => [
                ['id' => 'avis', 'label' => 'Avis'],
                ['id' => 'signalements', 'label' => 'Signalements'],
                ['id' => 'litiges', 'label' => 'Litiges'],
                ['id' => 'messages', 'label' => 'Messages'],
                ['id' => 'notifications', 'label' => 'Notifications'],
            ],
        ],
        [
            'id' => 'content',
            'label' => 'Contenu',
            'icon' => 'image',
            'items' => [
                ['id' => 'moderation-photos', 'label' => 'Photos'],
                ['id' => 'moderation-profils', 'label' => 'Profils'],
                ['id' => 'moderation-descriptions', 'label' => 'Descriptions'],
                ['id' => 'centre-aide', 'label' => 'Centre aide'],
                ['id' => 'faq', 'label' => 'FAQ'],
                ['id' => 'tutoriels', 'label' => 'Tutoriels'],
            ],
        ],
        [
            'id' => 'system',
            'label' => 'Systeme',
            'icon' => 'settings',
            'items' => [
                ['id' => 'journal', 'label' => 'Journal activite'],
                ['id' => 'securite', 'label' => 'Securite'],
                ['id' => 'parametres', 'label' => 'Parametres'],
                ['id' => 'corbeille', 'label' => 'Corbeille'],
            ],
        ],
    ];

    // Construction d'un tableau associatif ID -> Label pour tous les items
    // Utilisé plus tard pour afficher les sections génériques des modules non connectés
    $moduleLabels = [];
    foreach ($menuGroups as $group) {
        foreach ($group['items'] as $item) {
            $moduleLabels[$item['id']] = $item['label'];
        }
    }

    // ------------------------------------------------------------
    // 1.4 GÉNÉRATEUR D'ICÔNES SVG
    // Fonction anonyme qui retourne le code SVG d'une icône
    // en fonction de son nom et d'une classe CSS optionnelle.
    // Les chemins SVG sont des icônes Lucide (https://lucide.dev).
    // ------------------------------------------------------------
    $svgIcon = function ($name, $class = 'admin-icon') {
        // Tableau associatif contenant les chemins des icônes
        $icons = [
            'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'sparkles' => '<path d="m12 3 1.7 4.3L18 9l-4.3 1.7L12 15l-1.7-4.3L6 9l4.3-1.7L12 3Z"/><path d="m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14Z"/><path d="m5 14 .9 2.1L8 17l-2.1.9L5 20l-.9-2.1L2 17l2.1-.9L5 14Z"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/>',
            'tool' => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.8 2.8-2.8-2.8 2.6-3Z"/>',
            'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 11h8"/><path d="M8 16h6"/>',
            'bolt' => '<path d="M13 2 4 14h7l-1 8 10-13h-7l1-7Z"/>',
            'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
            'message' => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/><path d="M8 9h8"/><path d="M8 13h5"/>',
            'image' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4-4L7 21"/>',
            'settings' => '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.08a1.8 1.8 0 0 0-1-.52 1.8 1.8 0 0 0-1.98.36l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1H4a2 2 0 1 1 0-4h.08a1.8 1.8 0 0 0 .52-1 1.8 1.8 0 0 0-.36-1.98l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6V4a2 2 0 1 1 4 0v.08a1.8 1.8 0 0 0 1 .52 1.8 1.8 0 0 0 1.98-.36l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05A1.8 1.8 0 0 0 19.4 9c.22.31.42.65.6 1H20a2 2 0 1 1 0 4h-.08a1.8 1.8 0 0 0-.52 1Z"/>',
        ];

        // Construction du SVG avec le chemin correspondant (ou 'home' par défaut)
        return '<svg class="' . $class . '" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$name] ?? $icons['home']) . '</svg>';
    };
@endphp

{{-- 
============================================================
  2. LAYOUT PRINCIPAL
  La page hérite du layout <x-app-layout> (layout principal de l'app)
  qui contient probablement le header, le footer, les balises meta...
  On va utiliser Alpine.js pour gérer l'état de la page.
============================================================
--}}
<x-app-layout>

    {{-- 
    ============================================================
    2.1 DIV PRINCIPALE AVEC ALPINE.JS
    - x-data : définit l'état (state) de la page
    - x-init : (optionnel) initialisation
    - :class : lie la classe 'is-sidebar-collapsed' à l'état sidebarCollapsed
    - style : injecte les couleurs dynamiques en variables CSS
    ============================================================
    --}}
    <div
        x-data="{
            activeTab: 'dashboard',                // Onglet actif (dashboard par défaut)
            sidebarCollapsed: false,                // Menu réduit ou non (false = déplié)
            photoPreview: null,                     // URL de prévisualisation de la photo de profil
            openGroups: {                           // État d'ouverture/fermeture des groupes du menu
                users: true,                        // 'Utilisateurs' déplié par défaut
                ai: false,
                validation: true,                   // 'Validation artisans' déplié
                platform: false,
                missions: false,
                assignment: false,
                finance: false,
                support: false,
                content: false,
                system: false
            },
            // Méthode pour changer l'onglet actif
            setTab(tab) {
                this.activeTab = tab;
            },
            // Méthode pour basculer l'ouverture/fermeture d'un groupe
            toggleGroup(group) {
                this.openGroups[group] = !this.openGroups[group];
            },
            // Méthode qui génère un aperçu de la photo sélectionnée
            previewPhoto(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.photoPreview = null;
                    return;
                }
                this.photoPreview = URL.createObjectURL(file);
            }
        }"
        class="admin-page min-h-screen"
        :class="{ 'is-sidebar-collapsed': sidebarCollapsed }"
        style="
            --admin-primary: {{ $primary }};
            --admin-primary-dark: {{ $primaryDark }};
            --admin-primary-text: {{ $primaryText }};
            --admin-secondary: {{ $secondary }};
            --admin-bg: {{ $bg }};
            --admin-muted: {{ $muted }};
        "
    >

        {{-- 
        ============================================================
        2.2 STYLES CSS
        - Styles personnalisés utilisant les variables CSS
        - [x-cloak] : cache les éléments pendant le chargement d'Alpine
        - Classes utilitaires pour la sidebar, les cartes, les boutons...
        - Responsive : adapté aux écrans jusqu'à 720px
        ============================================================
        --}}
        <style>
            [x-cloak] { display: none !important; }

            .admin-page {
                --admin-side-panel-width: 420px; /* Largeur par défaut du panneau latéral */
                /* Dégradé de fond avec les couleurs dynamiques */
                background:
                    radial-gradient(circle at top left, color-mix(in srgb, var(--admin-primary) 14%, transparent), transparent 32rem),
                    linear-gradient(135deg, var(--admin-bg), #fff 48%, color-mix(in srgb, var(--admin-secondary) 8%, #fff));
                color: var(--admin-primary-text);
                /* Espace à droite pour le panneau latéral */
                padding-right: var(--admin-side-panel-width);
                transition: padding-right .28s ease;
            }

            .admin-page.is-side-panel-hidden {
                padding-right: 0;
            }

            .admin-workspace {
                min-height: 100vh;
            }

            /* Grille principale : sidebar + contenu */
            .admin-shell {
                display: grid;
                grid-template-columns: 310px minmax(0, 1fr);
                gap: 0;
                min-height: 100vh;
                transition: grid-template-columns .25s ease;
            }

            /* Cartes et panneaux avec effet verre (glassmorphism) */
            .admin-panel,
            .admin-card {
                border: 1px solid color-mix(in srgb, var(--admin-primary) 13%, transparent);
                background: rgba(255, 255, 255, .92);
                box-shadow: 0 24px 65px -46px color-mix(in srgb, var(--admin-primary-dark) 70%, #000);
                backdrop-filter: blur(16px);
            }

            /* Sidebar : fond sombre avec dégradé */
            .admin-sidebar {
                border-right: 1px solid rgba(255, 255, 255, .08);
                background:
                    radial-gradient(circle at top, color-mix(in srgb, var(--admin-primary) 28%, transparent), transparent 18rem),
                    linear-gradient(180deg, #0b0d13 0%, #11131a 48%, #07080d 100%);
                box-shadow: 0 24px 65px -42px rgba(0, 0, 0, .86);
                color: #fff;
                height: 100vh;
                overflow-y: auto;
                backdrop-filter: blur(16px);
            }

            /* Personnalisation du scrollbar de la sidebar */
            .admin-sidebar::-webkit-scrollbar { width: .45rem; }
            .admin-sidebar::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: rgba(255, 255, 255, .22);
            }

            /* Icône SVG : taille fixe */
            .admin-icon {
                width: 1.12rem;
                height: 1.12rem;
                flex: 0 0 auto;
            }

            /* Boutons du menu : communes à tous les niveaux */
            .admin-nav-button,
            .admin-group-button,
            .admin-sub-button {
                display: flex;
                align-items: center;
                width: 100%;
                border-radius: .55rem;
                color: rgba(255, 255, 255, .74);
                transition: background .2s, color .2s, transform .2s;
            }

            .admin-nav-button,
            .admin-group-button {
                gap: .72rem;
                padding: .78rem .85rem;
                font-weight: 800;
            }

            .admin-sub-button {
                justify-content: space-between;
                gap: .75rem;
                padding: .55rem .75rem .55rem 2.75rem;
                font-size: .88rem;
                font-weight: 700;
            }

            /* Survol : fond semi-transparent */
            .admin-nav-button:hover,
            .admin-group-button:hover,
            .admin-sub-button:hover {
                background: rgba(255, 255, 255, .08);
                color: #fff;
            }

            /* État actif : dégradé avec les couleurs primaires */
            .admin-nav-button.is-active,
            .admin-sub-button.is-active {
                background: linear-gradient(135deg, var(--admin-primary), color-mix(in srgb, var(--admin-secondary) 38%, var(--admin-primary-dark)));
                color: #fff;
                box-shadow: 0 14px 28px -18px var(--admin-primary);
            }

            .admin-sub-button.is-active {
                transform: translateX(2px);
            }

            /* Flèche d'ouverture des groupes (rotation) */
            .admin-chevron {
                width: .9rem;
                height: .9rem;
                transition: transform .2s;
            }

            .admin-chevron.is-open {
                transform: rotate(90deg);
            }

            /* Libellé des sections dans le menu (ex: "Gestion") */
            .admin-section-label {
                margin: 1rem .75rem .35rem;
                font-size: .68rem;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: rgba(255, 255, 255, .42);
            }

            /* Partie supérieure de la sidebar (logo + titre) */
            .admin-sidebar-top {
                border-color: rgba(255, 255, 255, .12);
            }

            /* Bouton pour réduire/étendre la sidebar */
            .admin-collapse-button {
                display: inline-flex;
                width: 2.35rem;
                height: 2.35rem;
                align-items: center;
                justify-content: center;
                border-radius: .55rem;
                color: rgba(255, 255, 255, .78);
                background: rgba(255, 255, 255, .08);
                transition: background .2s, color .2s;
            }

            .admin-collapse-button:hover {
                background: rgba(255, 255, 255, .14);
                color: #fff;
            }

            /* Carte de profil admin (photo + infos) */
            .admin-profile-card {
                border: 1px solid rgba(255, 255, 255, .1);
                background: rgba(255, 255, 255, .07);
            }

            /* Avatar (photo de profil) */
            .admin-avatar {
                width: 4.25rem;
                height: 4.25rem;
                border-radius: 999px;
                border: 2px solid rgba(255, 255, 255, .65);
                object-fit: cover;
                background: color-mix(in srgb, var(--admin-primary) 75%, #111);
            }

            /* Fallback si pas de photo : initiales */
            .admin-avatar-fallback {
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 1.45rem;
                font-weight: 900;
            }

            /* Bouton "Changer" pour upload de photo */
            .admin-upload-label {
                display: inline-flex;
                cursor: pointer;
                align-items: center;
                justify-content: center;
                border-radius: .5rem;
                padding: .55rem .75rem;
                font-size: .78rem;
                font-weight: 900;
                color: #fff;
                background: rgba(255, 255, 255, .12);
            }

            .admin-upload-label:hover {
                background: rgba(255, 255, 255, .18);
            }

            /* ============================================================
               MODE SIDEBAR COLLAPSÉ (menu réduit)
               ============================================================ */
            .is-sidebar-collapsed .admin-shell {
                grid-template-columns: 92px minmax(0, 1fr);
            }

            .is-sidebar-collapsed .admin-sidebar {
                padding-left: .7rem;
                padding-right: .7rem;
            }

            .is-sidebar-collapsed .admin-sidebar-top {
                justify-content: center;
            }

            .is-sidebar-collapsed .admin-sidebar-top > div {
                display: none; /* Masque le nom du site */
            }

            /* Masque les textes, sous-menus, étiquettes, chevrons */
            .is-sidebar-collapsed .admin-sidebar-text,
            .is-sidebar-collapsed .admin-section-label,
            .is-sidebar-collapsed .admin-chevron,
            .is-sidebar-collapsed .admin-sub-button,
            .is-sidebar-collapsed .admin-profile-details,
            .is-sidebar-collapsed .admin-sidebar-card {
                display: none !important;
            }

            .is-sidebar-collapsed .admin-nav-button,
            .is-sidebar-collapsed .admin-group-button {
                justify-content: center;
                padding-left: .7rem;
                padding-right: .7rem;
            }

            .is-sidebar-collapsed .admin-profile-card {
                padding: .65rem !important;
            }

            .is-sidebar-collapsed .admin-avatar {
                width: 3rem;
                height: 3rem;
            }

            /* Boutons d'action (primaires et secondaires) */
            .admin-action {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .55rem;
                padding: .7rem 1rem;
                font-size: .875rem;
                font-weight: 800;
                color: #fff;
                background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
                box-shadow: 0 14px 30px -18px var(--admin-primary-dark);
            }

            .admin-action.secondary {
                color: var(--admin-primary-text);
                background: color-mix(in srgb, var(--admin-primary) 8%, white);
                box-shadow: none;
            }

            .admin-sidebar .admin-action.secondary {
                color: #fff;
                background: rgba(255, 255, 255, .1);
            }

            .admin-sidebar .admin-action.secondary:hover {
                background: rgba(255, 255, 255, .16);
            }

            /* Badge de statut (ex: "En attente") */
            .admin-status {
                border-radius: 999px;
                padding: .25rem .7rem;
                font-size: .75rem;
                font-weight: 800;
                background: color-mix(in srgb, var(--admin-primary) 9%, white);
                color: var(--admin-primary-text);
                white-space: nowrap;
            }

            /* Grille des modules dans les sections "À connecter" */
            .admin-module-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }

            /* Responsive : pour les petits écrans, la sidebar devient horizontale */
            @media (max-width: 1080px) {
                .admin-page {
                    padding-right: 0;
                }
                .admin-shell {
                    grid-template-columns: 1fr;
                }
                .admin-sidebar {
                    height: auto;
                    max-height: none;
                    position: static;
                }
                .is-sidebar-collapsed .admin-shell {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 720px) {
                .admin-module-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="admin-workspace">
            <div class="admin-shell">

                {{-- 
                ============================================================
                3. SIDEBAR (barre latérale)
                Contient : logo, photo de profil, menu de navigation, déconnexion.
                ============================================================
                --}}
                <aside class="admin-sidebar p-4 lg:sticky lg:top-0 lg:self-start">

                    {{-- 3.1 En-tête de la sidebar : logo + titre + bouton collapse --}}
                    <div class="admin-sidebar-top flex items-center justify-between gap-3 border-b pb-4">
                        <div class="flex min-w-0 items-center gap-3">
                            @if ($logo)
                                {{-- Si un logo est défini, on l'affiche --}}
                                <img src="{{ asset($logo) }}" alt="{{ $siteName }}" class="h-11 w-11 rounded-lg object-cover">
                            @else
                                {{-- Sinon, on affiche la première lettre du nom en cercle --}}
                                <div class="flex h-11 w-11 items-center justify-center rounded-lg text-lg font-black text-white" style="background: var(--admin-primary)">
                                    {{ strtoupper(substr($siteName, 0, 1)) }}
                                </div>
                            @endif
                            <div class="admin-sidebar-text min-w-0">
                                <p class="truncate text-lg font-black">{{ $siteName }}</p>
                                <p class="text-sm text-white/55">Administration</p>
                            </div>
                        </div>
                        {{-- Bouton pour plier/déplier le menu --}}
                        <button type="button" class="admin-collapse-button" @click="sidebarCollapsed = !sidebarCollapsed" aria-label="Plier ou deplier le menu">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    {{-- 3.2 Formulaire de mise à jour de la photo de profil --}}
                    <form method="POST" action="{{ route('admin.profile-photo.update') }}" enctype="multipart/form-data" class="admin-profile-card mt-4 rounded-lg p-4">
                        @csrf
                        <div class="flex items-center gap-3">
                            {{-- Prévisualisation de la nouvelle photo (si une est sélectionnée) --}}
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="Apercu photo admin" class="admin-avatar">
                            </template>
                            {{-- Sinon, affiche la photo actuelle ou les initiales --}}
                            <template x-if="!photoPreview">
                                @if ($adminPhoto)
                                    <img src="{{ $adminPhoto }}" alt="{{ $adminUser->name }}" class="admin-avatar">
                                @else
                                    <div class="admin-avatar admin-avatar-fallback">
                                        {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                            </template>
                            <div class="admin-profile-details min-w-0 flex-1">
                                <p class="truncate font-black text-white">{{ $adminUser->name }}</p>
                                <p class="truncate text-sm text-white/55">{{ $adminUser->email }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    {{-- Champ d'upload caché, déclenché par le label --}}
                                    <label class="admin-upload-label">
                                        Changer
                                        <input type="file" name="profile_photo" class="hidden" accept="image/png,image/jpeg,image/webp" @change="previewPhoto">
                                    </label>
                                    <button type="submit" class="admin-upload-label" style="background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));">
                                        Enregistrer
                                    </button>
                                </div>
                                @error('profile_photo')
                                    <p class="mt-2 text-xs text-red-200">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </form>

                    {{-- 3.3 Carte d'information sur les candidatures en attente --}}
                    <div class="admin-sidebar-card mt-4 rounded-lg p-4 text-sm" style="background: rgba(255,255,255,.07); color: #fff">
                        <p class="font-black">{{ $pendingCount }} candidature(s) en attente</p>
                        <p class="mt-1 text-white/55">Priorite: validation artisans, documents et profils incomplets.</p>
                    </div>

                    {{-- 3.4 Navigation (menu principal) --}}
                    <nav class="mt-5">

                        {{-- Bouton "Tableau de bord" --}}
                        <button
                            type="button"
                            class="admin-nav-button"
                            :class="{ 'is-active': activeTab === 'dashboard' }"
                            @click="setTab('dashboard')"
                        >
                            {!! $svgIcon('home') !!}
                            <span class="admin-sidebar-text">Tableau de bord</span>
                        </button>

                        {{-- Libellé de section "Gestion" --}}
                        <p class="admin-section-label">Gestion</p>

                        {{-- Boucle sur chaque groupe du menu --}}
                        @foreach ($menuGroups as $group)
                            <div class="mb-1">
                                {{-- Bouton du groupe (cliquable pour ouvrir/fermer) --}}
                                <button type="button" class="admin-group-button" @click="toggleGroup('{{ $group['id'] }}')">
                                    {!! $svgIcon($group['icon']) !!}
                                    <span class="admin-sidebar-text min-w-0 flex-1 text-left">{{ $group['label'] }}</span>
                                    {{-- Flèche indiquant l'état d'ouverture --}}
                                    <svg class="admin-chevron" :class="{ 'is-open': openGroups.{{ $group['id'] }} }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                {{-- Liste des sous-items du groupe (affichée si ouvert) --}}
                                <div x-show="openGroups.{{ $group['id'] }}" class="space-y-1 pb-1" x-cloak>
                                    @foreach ($group['items'] as $item)
                                        <button
                                            type="button"
                                            class="admin-sub-button"
                                            :class="{ 'is-active': activeTab === '{{ $item['id'] }}' }"
                                            @click="setTab('{{ $item['id'] }}')"
                                        >
                                            <span class="truncate">{{ $item['label'] }}</span>
                                            {{-- Affichage d'un badge pour certains items (ex: nombre d'alertes) --}}
                                            @if (in_array($item['id'], ['validation-attente', 'artisans', 'parametres', 'notifications', 'ia-agent']))
                                                <span class="admin-status">
                                                    @if ($item['id'] === 'validation-attente')
                                                        {{ $pendingCount }}
                                                    @elseif ($item['id'] === 'artisans')
                                                        {{ $totalArtisans }}
                                                    @else
                                                        actif
                                                    @endif
                                                </span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>

                    {{-- 3.5 Bouton de déconnexion --}}
                    <form method="POST" action="{{ route('logout') }}" class="admin-sidebar-card mt-4">
                        @csrf
                        <button type="submit" class="admin-action secondary w-full">Deconnexion</button>
                    </form>

                </aside>

                {{-- 
                ============================================================
                4. CONTENU PRINCIPAL (zone des onglets)
                Chaque onglet est une section <section> avec x-show.
                Les onglets sont affichés en fonction de la valeur de activeTab.
                ============================================================
                --}}
                <main class="min-w-0 space-y-6 px-4 py-6 sm:px-6 lg:px-8">

                    {{-- 4.1 EN-TÊTE DU DASHBOARD (panneau avec héro et boutons d'action) --}}
                    <section class="admin-panel overflow-hidden rounded-lg">
                        <div class="grid gap-0 lg:grid-cols-[1.35fr_.65fr]">
                            <div class="p-6 sm:p-8">
                                <p class="text-sm font-black uppercase tracking-[.18em]" style="color: var(--admin-secondary)">Administration</p>
                                <h1 class="mt-3 text-3xl font-black sm:text-4xl">MaPage admin</h1>
                                <p class="mt-3 max-w-2xl text-base leading-7" style="color: var(--admin-muted)">
                                    Bonjour {{ Auth::user()->name }}. Pilote les utilisateurs, validations, missions, paiements et reglages de {{ $siteName }} depuis une seule interface.
                                </p>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ route('admin.artisans.index') }}" class="admin-action">Voir les candidatures</a>
                                    <a href="{{ route('admin.profils.index') }}" class="admin-action secondary">Profils partenaires</a>
                                </div>
                            </div>
                            {{-- Image de fond (hero) à droite --}}
                            <div class="min-h-64 bg-cover bg-center" style="background-image: linear-gradient(120deg, color-mix(in srgb, var(--admin-primary-dark) 54%, transparent), transparent), url('{{ asset($heroImage ?? 'images/artisan_btp.png') }}')"></div>
                        </div>
                    </section>

                    {{-- 4.2 ONGLET "TABLEAU DE BORD" (dashboard) --}}
                    <section x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">

                        {{-- 4.2.1 Statistiques (8 cartes) --}}
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($stats as $stat)
                                <article class="admin-card rounded-lg p-5">
                                    <p class="text-sm font-black" style="color: var(--admin-muted)">{{ $stat['label'] }}</p>
                                    <p class="mt-3 text-3xl font-black">{{ $stat['value'] }}</p>
                                    <p class="mt-2 text-sm" style="color: var(--admin-muted)">{{ $stat['hint'] }}</p>
                                </article>
                            @endforeach
                        </div>

                        {{-- 4.2.2 Dernières candidatures et alertes --}}
                        <div class="grid gap-4 xl:grid-cols-[1.25fr_.75fr]">
                            <article class="admin-card rounded-lg p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-black uppercase tracking-[.16em]" style="color: var(--admin-secondary)">Aujourd'hui</p>
                                        <h2 class="mt-2 text-2xl font-black">Priorites de gestion</h2>
                                    </div>
                                    <span class="admin-status">{{ $pendingCount + $incompleteProfilesCount }} point(s) a traiter</span>
                                </div>

                                <div class="mt-5 grid gap-3 md:grid-cols-3">
                                    <button type="button" class="rounded-lg bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" @click="setTab('validation-attente')">
                                        <p class="text-3xl font-black">{{ $pendingCount }}</p>
                                        <p class="mt-2 font-black">Candidatures</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Valider les nouveaux dossiers artisans.</p>
                                    </button>
                                    <button type="button" class="rounded-lg bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" @click="setTab('artisans')">
                                        <p class="text-3xl font-black">{{ $incompleteProfilesCount }}</p>
                                        <p class="mt-2 font-black">Profils incomplets</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Reperer les fiches a enrichir.</p>
                                    </button>
                                    <button type="button" class="rounded-lg bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" @click="setTab('notifications')">
                                        <p class="text-3xl font-black">{{ $totalUsers }}</p>
                                        <p class="mt-2 font-black">Utilisateurs</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Informer clients et partenaires.</p>
                                    </button>
                                </div>
                            </article>

                            <article class="admin-card rounded-lg p-5">
                                <h2 class="text-xl font-black">Etat des validations</h2>
                                <div class="mt-5 space-y-4">
                                    @php
                                        $validationRows = [
                                            ['label' => 'En attente', 'value' => $pendingCount, 'tab' => 'validation-attente', 'color' => 'var(--admin-secondary)'],
                                            ['label' => 'Valides', 'value' => $approvedCount, 'tab' => 'validation-valides', 'color' => '#16a34a'],
                                            ['label' => 'Refuses', 'value' => $rejectedCount, 'tab' => 'validation-refuses', 'color' => '#dc2626'],
                                        ];
                                    @endphp
                                    @foreach ($validationRows as $row)
                                        @php
                                            $percent = $totalArtisans > 0 ? round(($row['value'] / $totalArtisans) * 100) : 0;
                                        @endphp
                                        <button type="button" class="block w-full text-left" @click="setTab('{{ $row['tab'] }}')">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="font-black">{{ $row['label'] }}</span>
                                                <span class="text-sm font-black" style="color: {{ $row['color'] }}">{{ $row['value'] }} / {{ $totalArtisans }}</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white">
                                                <div class="h-full rounded-full" style="width: {{ $percent }}%; background: {{ $row['color'] }}"></div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </article>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-3">

                            {{-- Candidatures récentes --}}
                            <article class="admin-card rounded-lg p-5 lg:col-span-2">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-xl font-black">Candidatures recentes</h2>
                                    <a href="{{ route('admin.artisans.index') }}" class="text-sm font-black" style="color: var(--admin-primary)">Tout voir</a>
                                </div>
                                <div class="mt-4 divide-y" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                                    @forelse ($latestCandidates as $artisan)
                                        <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                                            <div>
                                                <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                                                <p class="text-sm" style="color: var(--admin-muted)">{{ ucfirst($artisan->profession) }} - {{ $artisan->intervention_area }}</p>
                                            </div>
                                            <a href="{{ route('admin.artisans.index') }}" class="admin-status">Verifier</a>
                                        </div>
                                    @empty
                                        <p class="py-5 text-sm" style="color: var(--admin-muted)">Aucune candidature en attente pour le moment.</p>
                                    @endforelse
                                </div>
                            </article>

                            {{-- Alertes importantes --}}
                            <article class="admin-card rounded-lg p-5">
                                <h2 class="text-xl font-black">Alertes importantes</h2>
                                <div class="mt-4 space-y-3">
                                    <div class="rounded-lg p-4" style="background: color-mix(in srgb, var(--admin-primary) 7%, white)">
                                        <p class="font-black">{{ $incompleteProfilesCount }} profil(s) incomplet(s)</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Documents, photos, adresse ou description a completer.</p>
                                    </div>
                                    <div class="rounded-lg p-4" style="background: color-mix(in srgb, var(--admin-secondary) 10%, white)">
                                        <p class="font-black">{{ $profileCount }} profil(s) deja enrichi(s)</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Prets pour la vitrine publique apres verification.</p>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-3">
                            <article class="admin-card rounded-lg p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-xl font-black">Metiers demandes</h2>
                                    <button type="button" class="admin-status" @click="setTab('metiers')">Gerer</button>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse ($professions as $profession)
                                        <div class="flex items-center justify-between gap-3 rounded-lg bg-white px-4 py-3">
                                            <span class="min-w-0 truncate font-bold">{{ ucfirst($profession->profession) }}</span>
                                            <span class="admin-status">{{ $profession->total }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm" style="color: var(--admin-muted)">Aucun metier enregistre.</p>
                                    @endforelse
                                </div>
                            </article>

                            <article class="admin-card rounded-lg p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-xl font-black">Zones actives</h2>
                                    <button type="button" class="admin-status" @click="setTab('zones')">Voir</button>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse ($areas as $area)
                                        <div class="flex items-center justify-between gap-3 rounded-lg bg-white px-4 py-3">
                                            <span class="min-w-0 truncate font-bold">{{ $area->intervention_area }}</span>
                                            <span class="admin-status">{{ $area->total }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm" style="color: var(--admin-muted)">Aucune zone enregistree.</p>
                                    @endforelse
                                </div>
                            </article>

                            <article class="admin-card rounded-lg p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-xl font-black">Derniers inscrits</h2>
                                    <button type="button" class="admin-status" @click="setTab('artisans')">Artisans</button>
                                </div>
                                <div class="mt-4 divide-y" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                                    @forelse ($latestArtisans as $artisan)
                                        <div class="py-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="min-w-0 truncate font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                                                <span class="admin-status">{{ $artisan->status ?? 'pending' }}</span>
                                            </div>
                                            <p class="mt-1 text-sm" style="color: var(--admin-muted)">
                                                {{ ucfirst($artisan->profession) }} - {{ $artisan->created_at?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="py-5 text-sm" style="color: var(--admin-muted)">Aucun artisan inscrit.</p>
                                    @endforelse
                                </div>
                            </article>
                        </div>
                    </section>

                    {{-- 4.3 ONGLET "CLIENTS" --}}
                    <section x-show="activeTab === 'clients'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Clients</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-lg bg-white p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Total clients</p>
                                <p class="mt-3 text-3xl font-black">{{ $customerCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Suspendus</p>
                                <p class="mt-3 text-3xl font-black">0</p>
                            </div>
                            <div class="rounded-lg bg-white p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Historique</p>
                                <p class="mt-3 text-3xl font-black">0</p>
                            </div>
                        </div>
                    </section>

                    {{-- 4.4 ONGLET "ARTISANS" (statistiques détaillées) --}}
                    <section x-show="activeTab === 'artisans'" x-cloak class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-4">
                            <article class="admin-card rounded-lg p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Total artisans</p>
                                <p class="mt-3 text-3xl font-black">{{ $totalArtisans }}</p>
                            </article>
                            <article class="admin-card rounded-lg p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Actifs</p>
                                <p class="mt-3 text-3xl font-black">{{ $approvedCount }}</p>
                            </article>
                            <article class="admin-card rounded-lg p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">En attente</p>
                                <p class="mt-3 text-3xl font-black">{{ $pendingCount }}</p>
                            </article>
                            <article class="admin-card rounded-lg p-5">
                                <p class="text-sm font-black" style="color: var(--admin-muted)">Suspendus</p>
                                <p class="mt-3 text-3xl font-black">0</p>
                            </article>
                        </div>
                        <div class="grid gap-4 lg:grid-cols-2">
                            <article class="admin-card rounded-lg p-5">
                                <h2 class="text-xl font-black">Metiers representes</h2>
                                <div class="mt-4 space-y-3">
                                    @forelse ($professions as $profession)
                                        <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3">
                                            <span class="font-bold">{{ ucfirst($profession->profession) }}</span>
                                            <span class="admin-status">{{ $profession->total }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm" style="color: var(--admin-muted)">Aucun metier a afficher.</p>
                                    @endforelse
                                </div>
                            </article>
                            <article class="admin-card rounded-lg p-5">
                                <h2 class="text-xl font-black">Zones couvertes</h2>
                                <div class="mt-4 space-y-3">
                                    @forelse ($areas as $area)
                                        <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3">
                                            <span class="font-bold">{{ $area->intervention_area }}</span>
                                            <span class="admin-status">{{ $area->total }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm" style="color: var(--admin-muted)">Aucune zone a afficher.</p>
                                    @endforelse
                                </div>
                            </article>
                        </div>
                    </section>

                    {{-- 4.5 ONGLET "ADMINISTRATEURS" --}}
                    <section x-show="activeTab === 'administrateurs'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Administrateurs</h2>
                        <div class="mt-5 rounded-lg bg-white p-5">
                            <p class="text-sm font-black" style="color: var(--admin-muted)">Comptes administrateurs</p>
                            <p class="mt-3 text-3xl font-black">{{ $adminCount }}</p>
                        </div>
                    </section>

                    {{-- 4.6 ONGLET "VALIDATION - EN ATTENTE" --}}
                    <section x-show="activeTab === 'validation-attente'" x-cloak class="admin-card rounded-lg p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-black">Validation artisans - en attente</h2>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Les nouveaux artisans a valider ou refuser.</p>
                            </div>
                            <a href="{{ route('admin.artisans.index') }}" class="admin-action">Ouvrir la gestion</a>
                        </div>
                        <div class="mt-5 overflow-hidden rounded-lg border" style="border-color: color-mix(in srgb, var(--admin-primary) 12%, transparent)">
                            @forelse ($latestCandidates as $artisan)
                                <div class="grid gap-3 border-b bg-white p-4 md:grid-cols-[1fr_1fr_auto]" style="border-color: color-mix(in srgb, var(--admin-primary) 10%, transparent)">
                                    <div>
                                        <p class="font-black">{{ $artisan->user->name ?? 'Artisan' }}</p>
                                        <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->user->email ?? 'Email non renseigne' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-bold">{{ ucfirst($artisan->profession) }}</p>
                                        <p class="text-sm" style="color: var(--admin-muted)">{{ $artisan->intervention_area }}</p>
                                    </div>
                                    <span class="admin-status self-center">En attente</span>
                                </div>
                            @empty
                                <p class="bg-white p-5 text-sm" style="color: var(--admin-muted)">Aucune candidature recente.</p>
                            @endforelse
                        </div>
                    </section>

                    {{-- 4.7 ONGLET "VALIDATION - VALIDÉS" --}}
                    <section x-show="activeTab === 'validation-valides'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Artisans valides</h2>
                        <p class="mt-2 text-sm" style="color: var(--admin-muted)">{{ $approvedCount }} artisan(s) sont actuellement valides.</p>
                        <a href="{{ route('admin.profils.index') }}" class="admin-action mt-5">Voir les profils</a>
                    </section>

                    {{-- 4.8 ONGLET "VALIDATION - REFUSÉS" --}}
                    <section x-show="activeTab === 'validation-refuses'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Artisans refuses</h2>
                        <p class="mt-2 text-sm" style="color: var(--admin-muted)">{{ $rejectedCount }} dossier(s) refuse(s) pour le moment.</p>
                    </section>

                    {{-- 4.9 ONGLET "MÉTIERS" --}}
                    <section x-show="activeTab === 'metiers'" x-cloak class="admin-card rounded-lg p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-black">Metiers</h2>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Gestion des categories de services proposees par les artisans.</p>
                            </div>
                            <button type="button" class="admin-action secondary">Ajouter plus tard</button>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse ($professions as $profession)
                                <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3">
                                    <span class="font-bold">{{ ucfirst($profession->profession) }}</span>
                                    <span class="admin-status">{{ $profession->total }} artisan(s)</span>
                                </div>
                            @empty
                                <p class="text-sm" style="color: var(--admin-muted)">Aucun metier a afficher.</p>
                            @endforelse
                        </div>
                    </section>

                    {{-- 4.10 ONGLET "ZONES" --}}
                    <section x-show="activeTab === 'zones'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Zones intervention</h2>
                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Villes, quartiers et regions couverts par les artisans.</p>
                        <div class="mt-4 space-y-3">
                            @forelse ($areas as $area)
                                <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3">
                                    <span class="font-bold">{{ $area->intervention_area }}</span>
                                    <span class="admin-status">{{ $area->total }} artisan(s)</span>
                                </div>
                            @empty
                                <p class="text-sm" style="color: var(--admin-muted)">Aucune zone a afficher.</p>
                            @endforelse
                        </div>
                    </section>

                    {{-- 4.11 ONGLET "NOTIFICATIONS" --}}
                    <section x-show="activeTab === 'notifications'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Notifications</h2>
                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <div class="rounded-lg bg-white p-5">
                                <p class="font-black">Tous</p>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Envoyer une annonce globale.</p>
                            </div>
                            <div class="rounded-lg bg-white p-5">
                                <p class="font-black">Artisans</p>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Informer les partenaires.</p>
                            </div>
                            <div class="rounded-lg bg-white p-5">
                                <p class="font-black">Clients</p>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Informer les particuliers.</p>
                            </div>
                        </div>
                    </section>

                    {{-- 4.12 ONGLET "PARAMÈTRES" --}}
                    <section x-show="activeTab === 'parametres'" x-cloak class="admin-card rounded-lg p-5">
                        <h2 class="text-xl font-black">Parametres</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-lg bg-white p-5">
                                <p class="font-black">Identite</p>
                                <p class="mt-2 text-sm" style="color: var(--admin-muted)">Nom: {{ $siteName }}</p>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">Contact: {{ $settings['contact_email'] ?? 'Non renseigne' }}</p>
                                <p class="mt-1 text-sm" style="color: var(--admin-muted)">WhatsApp: {{ $settings['contact_phone'] ?? 'Non renseigne' }}</p>
                            </div>
                            <div class="rounded-lg bg-white p-5">
                                <p class="font-black">Theme dynamique</p>
                                <div class="mt-3 flex gap-2">
                                    <span class="h-9 w-9 rounded-lg" style="background: var(--admin-primary)"></span>
                                    <span class="h-9 w-9 rounded-lg" style="background: var(--admin-primary-dark)"></span>
                                    <span class="h-9 w-9 rounded-lg" style="background: var(--admin-secondary)"></span>
                                    <span class="h-9 w-9 rounded-lg border" style="background: var(--admin-bg)"></span>
                                </div>
                                <p class="mt-3 text-sm" style="color: var(--admin-muted)">Commission, Mobile Money et delais seront branches ici.</p>
                            </div>
                        </div>
                    </section>

                    {{-- 4.13 ONGLETS GÉNÉRIQUES (pour les modules non encore connectés) --}}
                    @foreach ($moduleLabels as $moduleId => $moduleLabel)
                        @if (! in_array($moduleId, ['clients', 'artisans', 'administrateurs', 'validation-attente', 'validation-valides', 'validation-refuses', 'metiers', 'zones', 'notifications', 'parametres']))
                            <section x-show="activeTab === '{{ $moduleId }}'" x-cloak class="admin-card rounded-lg p-5">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-black uppercase tracking-[.16em]" style="color: var(--admin-secondary)">Module</p>
                                        <h2 class="mt-2 text-2xl font-black">{{ $moduleLabel }}</h2>
                                        <p class="mt-2 max-w-2xl text-sm leading-6" style="color: var(--admin-muted)">
                                            Cette section est deja prevue dans MaPage admin. Elle pourra etre connectee a la base de donnees quand le module correspondant sera cree.
                                        </p>
                                    </div>
                                    <span class="admin-status">A connecter</span>
                                </div>
                                <div class="admin-module-grid mt-5">
                                    <div class="rounded-lg bg-white p-5">
                                        <p class="font-black">Donnees</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Listes, filtres et details seront affiches ici.</p>
                                    </div>
                                    <div class="rounded-lg bg-white p-5">
                                        <p class="font-black">Actions</p>
                                        <p class="mt-1 text-sm" style="color: var(--admin-muted)">Ajouter, modifier, suspendre, rembourser ou moderer selon le module.</p>
                                    </div>
                                </div>
                            </section>
                        @endif
                    @endforeach

                </main>
            </div>
        </div>

        {{-- 
        ============================================================
        5. PANNEAU LATÉRAL 
        - Fixé à droite, prenant toute la hauteur.
        - Peut être fermé, rouvert, et redimensionné horizontalement.
        - La largeur est sauvegardée dans le localStorage du navigateur.
        ============================================================
        --}}
        <div id="side-panel" class="admin-side-panel">
            {{-- Poignée de redimensionnement --}}
            <div class="admin-side-panel__resize-handle" id="side-panel-resize-handle"></div>

            {{-- En-tête du panneau --}}
            <div class="admin-side-panel__header">
                <div class="admin-side-panel__title">
                    <span class="admin-side-panel__dot"></span>
                    Mon panneau
                </div>
                {{-- Bouton fermer --}}
                <button type="button" id="side-panel-close-btn" class="admin-side-panel__icon-btn" title="Masquer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            {{-- Contenu du panneau (modifiable) --}}
            <div class="admin-side-panel__content">
                <p>Contenu du panneau ici. Il prend toute la hauteur, du haut jusqu'en bas, et se redimensionne comme dans Claude.</p>
            </div>
        </div>

        {{-- Bouton pour rouvrir le panneau (visible quand il est fermé) --}}
        <button type="button" id="side-panel-reopen-btn" class="admin-side-panel__reopen-tab" title="Afficher le panneau">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        {{-- Styles spécifiques au panneau latéral --}}
        <style>
            .admin-side-panel {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: var(--admin-side-panel-width);
                z-index: 1000;
                display: flex;
                flex-direction: column;
                background:
                    radial-gradient(circle at top left, color-mix(in srgb, var(--admin-primary) 18%, transparent), transparent 18rem),
                    linear-gradient(180deg, #090b10 0%, #11131a 48%, #05060a 100%);
                border-left: 1px solid rgba(255, 255, 255, .1);
                box-shadow: -18px 0 44px -34px rgba(0, 0, 0, .9);
                color: #fff;
                transform: translateX(0);
                transition: transform .28s ease, width .1s ease;
            }

            .admin-side-panel.is-hidden {
                transform: translateX(100%);
            }

            .admin-side-panel__resize-handle {
                position: absolute;
                top: 0;
                left: -4px;
                width: 8px;
                height: 100%;
                cursor: col-resize;
                z-index: 5;
                background: transparent;
            }

            .admin-side-panel__resize-handle:hover,
            .admin-side-panel__resize-handle.is-active {
                background: color-mix(in srgb, var(--admin-primary) 44%, transparent);
            }

            .admin-side-panel__header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 18px;
                border-bottom: 1px solid rgba(255, 255, 255, .1);
                flex-shrink: 0;
            }

            .admin-side-panel__title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 800;
                font-size: .95rem;
                color: #fff;
            }

            .admin-side-panel__dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: color-mix(in srgb, var(--admin-primary) 78%, #fff);
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary) 24%, transparent);
            }

            .admin-side-panel__icon-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                border: none;
                background: transparent;
                color: rgba(255, 255, 255, .66);
                cursor: pointer;
                transition: background .2s ease, color .2s ease;
            }

            .admin-side-panel__icon-btn svg {
                width: 18px;
                height: 18px;
            }

            .admin-side-panel__icon-btn:hover {
                background: rgba(255, 255, 255, .1);
                color: #fff;
            }

            .admin-side-panel__content {
                flex: 1;
                overflow-y: auto;
                padding: 18px;
                color: rgba(255, 255, 255, .76);
                font-size: .9rem;
                line-height: 1.5;
            }

            .admin-side-panel__reopen-tab {
                position: fixed;
                top: 50%;
                right: 0;
                transform: translateY(-50%) translateX(100%);
                width: 32px;
                height: 64px;
                border: none;
                border-radius: 10px 0 0 10px;
                background: linear-gradient(180deg, #11131a, #05060a);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: -6px 0 18px -8px rgba(0, 0, 0, .85);
                z-index: 999;
                opacity: 0;
                pointer-events: none;
                transition: transform .35s cubic-bezier(.4, 0, .2, 1), opacity .25s ease;
            }

            .admin-side-panel__reopen-tab svg {
                width: 18px;
                height: 18px;
            }

            .admin-side-panel__reopen-tab.is-visible {
                transform: translateY(-50%) translateX(0);
                opacity: 1;
                pointer-events: auto;
            }

            @media (max-width: 768px) {
                .admin-side-panel {
                    width: 100% !important;
                }
            }
        </style>

        {{-- 
        ============================================================
        6. JAVASCRIPT (pour le panneau latéral)
        Gère l'ouverture/fermeture, le redimensionnement,
        et la sauvegarde de la largeur dans localStorage.
        ============================================================
        --}}
        <script>
            (function () {
                // Récupération des éléments DOM
                const panel = document.getElementById('side-panel');
                const handle = document.getElementById('side-panel-resize-handle');
                const closeBtn = document.getElementById('side-panel-close-btn');
                const reopenBtn = document.getElementById('side-panel-reopen-btn');
                const page = panel.closest('.admin-page'); // Élément parent

                const STORAGE_KEY = 'adminSidePanelWidth';
                const MIN_WIDTH = 300;
                const MAX_WIDTH = 720;

                // Chargement de la largeur sauvegardée dans localStorage
                const savedWidth = localStorage.getItem(STORAGE_KEY);
                if (savedWidth) {
                    const width = Math.max(MIN_WIDTH, Math.min(MAX_WIDTH, Number(savedWidth)));
                    page.style.setProperty('--admin-side-panel-width', width + 'px');
                }

                // Fermer le panneau
                closeBtn.addEventListener('click', function () {
                    panel.classList.add('is-hidden');
                    page.classList.add('is-side-panel-hidden');
                    reopenBtn.classList.add('is-visible');
                });

                // Rouvrir le panneau
                reopenBtn.addEventListener('click', function () {
                    panel.classList.remove('is-hidden');
                    page.classList.remove('is-side-panel-hidden');
                    reopenBtn.classList.remove('is-visible');
                });

                // Gestion du redimensionnement
                let isResizing = false;

                handle.addEventListener('mousedown', function () {
                    isResizing = true;
                    handle.classList.add('is-active');
                    document.body.style.userSelect = 'none'; // Empêche la sélection de texte
                    document.body.style.cursor = 'col-resize';
                });

                document.addEventListener('mousemove', function (e) {
                    if (!isResizing) return;
                    // Calcule la nouvelle largeur en fonction de la position de la souris
                    let newWidth = window.innerWidth - e.clientX;
                    newWidth = Math.max(MIN_WIDTH, Math.min(MAX_WIDTH, newWidth));
                    page.style.setProperty('--admin-side-panel-width', newWidth + 'px');
                });

                document.addEventListener('mouseup', function () {
                    if (!isResizing) return;
                    isResizing = false;
                    handle.classList.remove('is-active');
                    document.body.style.userSelect = '';
                    document.body.style.cursor = '';
                    // Sauvegarde de la largeur actuelle
                    localStorage.setItem(STORAGE_KEY, panel.offsetWidth);
                });
            })();
        </script>

    </div>
</x-app-layout>
```
