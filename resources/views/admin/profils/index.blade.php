<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profils des Artisans - {{ $settings['site_name'] ?? 'Artilo' }}</title>
    
    @php
        $primary = $settings['color_primary'] ?? '#7C3AED';
        $primaryDark = $settings['color_primary_dark'] ?? '#6D28D9';
        $primaryLight = $settings['color_primary_light'] ?? '#A78BFA';
        $secondary = $settings['color_secondary'] ?? '#D97706';
        $secondaryLight = $settings['color_secondary_light'] ?? '#F59E0B';
        $bg = $settings['color_bg_2'] ?? '#FAF8FF';
        $textColor = $settings['color_text'] ?? '#2E1065';
        $muted = $settings['color_muted'] ?? '#6B5B95';
        $logo = $settings['logo'] ?? null;
        $siteName = $settings['site_name'] ?? 'Artilo';
    @endphp

    <style>
        :root {
            --primary: {{ $primary }};
            --primary-dark: {{ $primaryDark }};
            --primary-light: {{ $primaryLight }};
            --secondary: {{ $secondary }};
            --secondary-light: {{ $secondaryLight }};
            --bg: {{ $bg }};
            --text: {{ $textColor }};
            --muted: {{ $muted }};
            --shadow: 0 4px 24px rgba(0,0,0,0.06);
            --radius: 16px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius);
            padding: 32px 40px;
            margin-bottom: 32px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-left .logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: cover;
            background: rgba(255,255,255,0.15);
            padding: 4px;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .header-left .logo-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .header-text p {
            opacity: 0.85;
            font-size: 15px;
            margin-top: 4px;
        }

        .header-stats {
            display: flex;
            gap: 32px;
            background: rgba(255,255,255,0.12);
            padding: 12px 28px;
            border-radius: 12px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .header-stats .stat-item {
            text-align: center;
        }

        .header-stats .stat-number {
            font-size: 24px;
            font-weight: 700;
        }

        .header-stats .stat-label {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* ===== STATS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            padding: 20px 24px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(124, 58, 237, 0.06);
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }

        .stat-card .stat-icon {
            font-size: 22px;
            margin-bottom: 8px;
            display: inline-block;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: var(--muted);
            font-weight: 500;
        }

        .stat-card.accent-primary .stat-value { color: var(--primary); }
        .stat-card.accent-secondary .stat-value { color: var(--secondary); }
        .stat-card.accent-success .stat-value { color: #16a34a; }
        .stat-card.accent-warning .stat-value { color: #dc2626; }

        /* ===== TOOLBAR ===== */
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
            align-items: center;
            background: white;
            padding: 16px 24px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(124, 58, 237, 0.06);
        }

        .search-wrapper {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-wrapper input {
            width: 100%;
            padding: 10px 16px 10px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            background: var(--bg);
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }

        .search-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        .search-wrapper .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 18px;
        }

        .filter-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-group select {
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            background: var(--bg);
            cursor: pointer;
            transition: border-color var(--transition);
            outline: none;
            min-width: 140px;
        }

        .filter-group select:focus {
            border-color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(124, 58, 237, 0.35);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            background: rgba(124, 58, 237, 0.05);
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 8px;
        }

        /* ===== CARDS GRID ===== */
        .artisans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .artisan-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(124, 58, 237, 0.06);
            overflow: hidden;
            transition: all var(--transition);
            animation: fadeInUp 0.5s ease both;
        }

        .artisan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px rgba(0,0,0,0.08);
            border-color: rgba(124, 58, 237, 0.12);
        }

        .artisan-card-header {
            padding: 20px 24px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .artisan-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--bg);
            border: 3px solid rgba(124, 58, 237, 0.12);
            flex-shrink: 0;
        }

        .artisan-avatar-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .artisan-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .artisan-profession {
            font-size: 14px;
            color: var(--muted);
            font-weight: 500;
        }

        .artisan-card-body {
            padding: 16px 24px 20px;
        }

        .artisan-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .artisan-info-item {
            display: flex;
            flex-direction: column;
        }

        .artisan-info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            font-weight: 600;
        }

        .artisan-info-item .value {
            font-size: 14px;
            font-weight: 600;
            margin-top: 2px;
            color: var(--text);
        }

        .artisan-info-item .value.truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        .artisan-photos {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .artisan-photos img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #f3f4f6;
            transition: transform var(--transition);
            cursor: pointer;
        }

        .artisan-photos img:hover {
            transform: scale(1.05);
            border-color: var(--primary);
        }

        .artisan-photos .no-photo {
            font-size: 13px;
            color: var(--muted);
            padding: 8px 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-available {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-unavailable {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-verified {
            background: #dbeafe;
            color: #2563eb;
        }

        .artisan-card-footer {
            padding: 14px 24px 20px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn-view {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            background: rgba(124, 58, 237, 0.08);
            border: 2px solid transparent;
            text-decoration: none;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.25);
        }

        /* ===== PAGINATION ===== */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px 0;
        }

        .pagination-wrapper .info {
            font-size: 14px;
            color: var(--muted);
        }

        .pagination-wrapper .links {
            display: flex;
            gap: 6px;
        }

        .pagination-wrapper .links a,
        .pagination-wrapper .links span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition);
            border: 2px solid transparent;
        }

        .pagination-wrapper .links a {
            color: var(--text);
            background: white;
            border-color: #e5e7eb;
        }

        .pagination-wrapper .links a:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .pagination-wrapper .links .active span {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-color: var(--primary);
        }

        .pagination-wrapper .links .disabled span {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .empty-state .empty-icon {
            font-size: 72px;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 24px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--muted);
            max-width: 400px;
            margin: 0 auto;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .artisan-card:nth-child(1) { animation-delay: 0.02s; }
        .artisan-card:nth-child(2) { animation-delay: 0.06s; }
        .artisan-card:nth-child(3) { animation-delay: 0.10s; }
        .artisan-card:nth-child(4) { animation-delay: 0.14s; }
        .artisan-card:nth-child(5) { animation-delay: 0.18s; }
        .artisan-card:nth-child(6) { animation-delay: 0.22s; }
        .artisan-card:nth-child(7) { animation-delay: 0.26s; }
        .artisan-card:nth-child(8) { animation-delay: 0.30s; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .header-stats {
                padding: 12px 20px;
                gap: 20px;
            }
            .header-stats .stat-number { font-size: 20px; }
        }

        @media (max-width: 768px) {
            body { padding: 16px; }
            .header { padding: 24px; }
            .header-content { flex-direction: column; align-items: stretch; }
            .header-stats { justify-content: space-around; }
            .header-text h1 { font-size: 22px; }
            .artisans-grid { grid-template-columns: 1fr; }
            .artisan-info-grid { grid-template-columns: 1fr; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .filter-group { flex-wrap: wrap; }
            .filter-group select { flex: 1; min-width: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .pagination-wrapper { flex-direction: column; align-items: center; text-align: center; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .header-stats { flex-direction: column; gap: 8px; align-items: center; }
            .header-left { flex-direction: column; text-align: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.01ms !important; }
            .artisan-card { animation: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- HEADER --}}
        <header class="header">
            <div class="header-content">
                <div class="header-left">
                    @if($logo)
                        <img src="{{ asset($logo) }}" alt="{{ $siteName }}" class="logo">
                    @else
                        <div class="logo-placeholder">{{ substr($siteName, 0, 1) }}</div>
                    @endif
                    <div class="header-text">
                        <h1>Profils des Artisans</h1>
                        <p>Gérez les profils des prestataires partenaires</p>
                    </div>
                </div>
                <div class="header-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ $artisans->total() }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                    @php
                        $available = $artisans->filter(fn($a) => $a->availability_start_time && $a->availability_end_time)->count();
                    @endphp
                    <div class="stat-item">
                        <div class="stat-number">{{ $available }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                    @php
                        $verified = $artisans->filter(fn($a) => $a->verified ?? false)->count();
                    @endphp
                    <div class="stat-item">
                        <div class="stat-number">{{ $verified }}</div>
                        <div class="stat-label">Vérifiés</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- STATS CARDS --}}
        <div class="stats-grid">
            <div class="stat-card accent-primary">
                <div class="stat-icon">👷</div>
                <div class="stat-value">{{ $artisans->total() }}</div>
                <div class="stat-label">Artisans partenaires</div>
            </div>
            <div class="stat-card accent-secondary">
                <div class="stat-icon">📋</div>
                <div class="stat-value">{{ $professionsCount ?? 0 }}</div>
                <div class="stat-label">Métiers différents</div>
            </div>
            <div class="stat-card accent-success">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $verified }}</div>
                <div class="stat-label">Profils vérifiés</div>
            </div>
            <div class="stat-card accent-warning">
                <div class="stat-icon">⏳</div>
                <div class="stat-value">{{ $pendingProfiles ?? 0 }}</div>
                <div class="stat-label">En attente de validation</div>
            </div>
        </div>

        {{-- TOOLBAR --}}
        <div class="toolbar">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Rechercher un artisan..." aria-label="Rechercher un artisan">
            </div>
            <div class="filter-group">
                <select id="professionFilter" aria-label="Filtrer par métier">
                    <option value="">Tous les métiers</option>
                    @foreach($professionsList ?? [] as $profession)
                        <option value="{{ $profession }}">{{ ucfirst($profession) }}</option>
                    @endforeach
                </select>
                <select id="availabilityFilter" aria-label="Filtrer par disponibilité">
                    <option value="">Tous</option>
                    <option value="available">Disponibles</option>
                    <option value="unavailable">Indisponibles</option>
                </select>
                <button class="btn btn-primary" id="resetFilters">🔄 Réinitialiser</button>
            </div>
        </div>

        {{-- ARTISANS GRID --}}
        @if($artisans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏗️</div>
                <h3>Aucun profil d'artisan</h3>
                <p>Aucun artisan n'a encore rempli son profil. Les profils apparaîtront ici une fois les artisans inscrits.</p>
            </div>
        @else
            <div class="artisans-grid" id="artisansGrid">
                @foreach($artisans as $artisan)
                    <div class="artisan-card" data-profession="{{ strtolower($artisan->profession ?? $artisan->main_profession ?? '') }}" data-available="{{ $artisan->availability_start_time && $artisan->availability_end_time ? '1' : '0' }}">
                        <div class="artisan-card-header">
                            @if($artisan->user->profile_photo)
                                <img src="{{ asset('storage/' . $artisan->user->profile_photo) }}" alt="{{ $artisan->user->name }}" class="artisan-avatar">
                            @else
                                <div class="artisan-avatar-placeholder">{{ strtoupper(substr($artisan->user->name, 0, 1)) }}</div>
                            @endif
                            <div>
                                <div class="artisan-name">{{ $artisan->user->name }}</div>
                                <div class="artisan-profession">{{ ucfirst($artisan->main_profession ?? $artisan->profession ?? 'Métier non renseigné') }}</div>
                            </div>
                            @if($artisan->verified ?? false)
                                <span class="badge badge-verified" style="margin-left: auto;">✓ Vérifié</span>
                            @endif
                        </div>
                        <div class="artisan-card-body">
                            <div class="artisan-info-grid">
                                <div class="artisan-info-item">
                                    <span class="label">Expérience</span>
                                    <span class="value">{{ $artisan->years_experience ?? 0 }} ans</span>
                                </div>
                                <div class="artisan-info-item">
                                    <span class="label">Disponibilité</span>
                                    <span class="value">
                                        @if($artisan->availability_start_time && $artisan->availability_end_time)
                                            <span class="badge badge-available">
                                                {{ $artisan->availability_start_time->format('H:i') }} - {{ $artisan->availability_end_time->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="badge badge-unavailable">Non renseignée</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="artisan-info-item">
                                    <span class="label">Localisation</span>
                                    <span class="value truncate">{{ $artisan->address ?? 'Non renseignée' }}</span>
                                </div>
                                <div class="artisan-info-item">
                                    <span class="label">Zone d'intervention</span>
                                    <span class="value truncate">{{ $artisan->intervention_area ?? 'Non renseignée' }}</span>
                                </div>
                            </div>

                            @if(!empty($artisan->photos))
                                <div class="artisan-photos">
                                    @foreach(array_slice($artisan->photos, 0, 4) as $photo)
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Photo artisan" loading="lazy">
                                    @endforeach
                                    @if(count($artisan->photos) > 4)
                                        <div style="display:flex;align-items:center;padding:0 8px;font-size:13px;color:var(--muted);font-weight:600;">+{{ count($artisan->photos) - 4 }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="artisan-photos">
                                    <span class="no-photo">Aucune photo disponible</span>
                                </div>
                            @endif
                        </div>
                        <div class="artisan-card-footer">
                            <span style="font-size:13px;color:var(--muted);">
                                Inscrit le {{ $artisan->created_at->format('d/m/Y') }}
                            </span>
                            <a href="{{ route('admin.profils.show', $artisan->id) }}" class="btn-view">
                                👁️ Voir le profil
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            <div class="pagination-wrapper">
                <div class="info">
                    Affichage de {{ $artisans->firstItem() ?? 0 }} à {{ $artisans->lastItem() ?? 0 }} sur {{ $artisans->total() }} artisans
                </div>
                <div class="links">
                    {{ $artisans->links() }}
                </div>
            </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const professionFilter = document.getElementById('professionFilter');
            const availabilityFilter = document.getElementById('availabilityFilter');
            const resetBtn = document.getElementById('resetFilters');
            const cards = document.querySelectorAll('.artisan-card');

            function filterCards() {
                const search = searchInput.value.toLowerCase().trim();
                const profession = professionFilter.value.toLowerCase();
                const availability = availabilityFilter.value;

                cards.forEach(card => {
                    const name = card.querySelector('.artisan-name')?.textContent?.toLowerCase() || '';
                    const professionText = card.querySelector('.artisan-profession')?.textContent?.toLowerCase() || '';
                    const address = card.querySelector('.artisan-info-item .value.truncate')?.textContent?.toLowerCase() || '';
                    const cardProfession = card.dataset.profession || '';
                    const isAvailable = card.dataset.available === '1';

                    let match = true;

                    if (search) {
                        const matchName = name.includes(search);
                        const matchProf = professionText.includes(search);
                        const matchAddr = address.includes(search);
                        if (!(matchName || matchProf || matchAddr)) {
                            match = false;
                        }
                    }

                    if (profession && match) {
                        if (!cardProfession.includes(profession)) {
                            match = false;
                        }
                    }

                    if (availability && match) {
                        if (availability === 'available' && !isAvailable) {
                            match = false;
                        } else if (availability === 'unavailable' && isAvailable) {
                            match = false;
                        }
                    }

                    card.style.display = match ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterCards);
            professionFilter.addEventListener('change', filterCards);
            availabilityFilter.addEventListener('change', filterCards);

            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                professionFilter.value = '';
                availabilityFilter.value = '';
                filterCards();
            });

            // Photo preview on click (lightbox simple)
            document.querySelectorAll('.artisan-photos img').forEach(img => {
                img.addEventListener('click', function() {
                    const overlay = document.createElement('div');
                    overlay.style.cssText = `
                        position: fixed; inset: 0; background: rgba(0,0,0,0.8);
                        display: flex; align-items: center; justify-content: center;
                        z-index: 9999; cursor: pointer;
                        backdrop-filter: blur(8px);
                    `;
                    const clone = this.cloneNode();
                    clone.style.cssText = `
                        max-width: 90vw; max-height: 90vh;
                        border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,0.4);
                        object-fit: contain;
                    `;
                    overlay.appendChild(clone);
                    overlay.addEventListener('click', () => overlay.remove());
                    document.body.appendChild(overlay);
                });
            });
        });
    </script>
</body>
</html>