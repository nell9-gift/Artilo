{{-- resources/views/admin/profils/show.blade.php --}}
@extends('layouts.app') {{-- ou le layout que vous utilisez pour l'admin --}}

@section('title', 'Détail du profil - ' . $artisan->user->name)

@section('header')
    {{-- Rien --}}
@endsection

@section('content')
@php
    // Récupération des couleurs depuis la table settings
    $primary = \App\Models\Setting::where('key', 'color_primary')->first()?->value ?? '#7C3AED';
    $secondary = \App\Models\Setting::where('key', 'color_secondary')->first()?->value ?? '#D97706';
    $bg2 = \App\Models\Setting::where('key', 'color_bg_2')->first()?->value ?? '#FAF8FF';
    $text = \App\Models\Setting::where('key', 'color_text')->first()?->value ?? '#2E1065';
    // Calcul du taux de complétion
    $fields = [
        'main_profession', 'years_experience', 'description', 'company_name',
        'intervention_region', 'intervention_city', 'address',
        'mobile_money_number', 'identity_document_number'
    ];
    $filled = 0;
    foreach ($fields as $f) {
        if (filled($artisan->$f)) $filled++;
    }
    if (!empty($artisan->photos)) $filled++;
    $total = count($fields) + 1;
    $completion = round(($filled / $total) * 100);

    // Labels de statut
    $statusLabels = [
        'pending'   => 'En attente',
        'approved'  => 'Validé',
        'rejected'  => 'Refusé',
    ];
    $statusClass = $artisan->status === 'approved' ? 'badge-approved' : ($artisan->status === 'rejected' ? 'badge-rejected' : 'badge-pending');
    $statusIcon = $artisan->status === 'approved' ? 'fa-check-circle' : ($artisan->status === 'rejected' ? 'fa-times-circle' : 'fa-clock');
    $statusLabel = $statusLabels[$artisan->status] ?? ucfirst($artisan->status);
@endphp

{{-- Styles personnalisés --}}
<style>
    /* Variables de couleur */
    :root {
        --primary: {{ $primary }};
        --secondary: {{ $secondary }};
        --bg-2: {{ $bg2 }};
        --text: {{ $text }};
        --shadow: 0 20px 60px -20px rgba(124,58,237,0.35);
        --radius: 1.5rem;
    }
    .profile-show {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background: var(--bg-2);
        padding: 2rem 1rem;
        min-height: 100vh;
    }
    .profile-show .container {
        max-width: 1280px;
        margin: 0 auto;
    }
    .card {
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(124,58,237,0.08);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: fit-content;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 28px 70px -20px rgba(124,58,237,0.45);
    }
    .section-title {
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        border-bottom: 2px solid rgba(124,58,237,0.15);
        padding-bottom: 0.6rem;
        margin-bottom: 1.2rem;
    }
    .section-title i {
        color: var(--primary);
        width: 1.4rem;
        text-align: center;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem 1.5rem;
    }
    .info-item {
        display: flex;
        flex-direction: column;
    }
    .info-item .label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9CA3AF;
    }
    .info-item .value {
        font-weight: 600;
        color: var(--text);
        margin-top: 0.1rem;
        word-break: break-word;
    }
    .badge {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: 1px solid transparent;
    }
    .badge-approved {
        background: #D1FAE5;
        color: #065F46;
        border-color: #10B981;
    }
    .badge-pending {
        background: #FEF3C7;
        color: #92400E;
        border-color: #F59E0B;
    }
    .badge-rejected {
        background: #FEE2E2;
        color: #991B1B;
        border-color: #EF4444;
    }
    .tag {
        display: inline-block;
        background: #F3F4F6;
        padding: 0.15rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #374151;
        margin: 0.15rem 0.1rem;
    }
    .progress-bar {
        height: 8px;
        background: #E5E7EB;
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 0.4rem;
    }
    .progress-bar .fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        border-radius: 9999px;
        transition: width 0.8s ease;
    }
    .profile-picture {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--primary);
    }
    .profile-picture-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #E5E7EB;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #9CA3AF;
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--primary);
    }
    .id-photos {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.5rem;
    }
    .id-photos figure {
        margin: 0;
        text-align: center;
        background: #F9FAFB;
        padding: 0.5rem;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
    }
    .id-photos img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    .id-photos figcaption {
        font-size: 0.65rem;
        color: #6B7280;
        margin-top: 0.25rem;
    }
    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }
    .photo-gallery img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        transition: transform 0.2s ease;
    }
    .photo-gallery img:hover {
        transform: scale(1.03);
    }
    #map {
        height: 260px;
        border-radius: 1rem;
        border: 1px solid #E5E7EB;
        margin-top: 0.75rem;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #6B7280;
        color: white;
        padding: 0.7rem 1.8rem;
        border-radius: 9999px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-back:hover {
        background: #4B5563;
        color: white;
    }
    @media (max-width: 640px) {
        .info-grid { grid-template-columns: 1fr; }
        .photo-gallery { grid-template-columns: repeat(2, 1fr); }
        .id-photos img { width: 90px; height: 90px; }
        .card { padding: 1.25rem; }
        .profile-picture, .profile-picture-placeholder { width: 60px; height: 60px; }
    }
    /* Ajout de la police Inter via Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
</style>

<div class="profile-show">
    <div class="container">

        {{-- EN-TÊTE --}}
        <div class="card mb-6">
            <div class="flex flex-wrap items-center gap-6">
                @if($artisan->user->profile_photo)
                    <img src="{{ asset('storage/' . $artisan->user->profile_photo) }}" alt="Photo de profil" class="profile-picture">
                @else
                    <div class="profile-picture-placeholder"><i class="fas fa-user"></i></div>
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-black text-gray-800">{{ $artisan->user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }} mr-1"></i> {{ $statusLabel }}
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i> Inscrit le {{ $artisan->created_at->format('d/m/Y') }}
                        </span>
                        @if($artisan->average_rating && $artisan->average_rating > 0)
                            <span class="text-sm text-yellow-600">
                                <i class="fas fa-star"></i> {{ number_format($artisan->average_rating, 1) }} / 5
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1 text-sm">
                        {{ $artisan->main_profession ?? $artisan->profession ?? 'Métier non renseigné' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- STATISTIQUES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card text-center">
                <div class="text-3xl font-black" style="color: var(--primary);">{{ $artisan->average_rating && $artisan->average_rating > 0 ? number_format($artisan->average_rating, 1) : '—' }}</div>
                <div class="text-sm text-gray-500">Note moyenne</div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-black" style="color: var(--primary);">0</div>
                <div class="text-sm text-gray-500">Missions terminées</div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-black" style="color: var(--primary);">{{ $completion }}%</div>
                <div class="text-sm text-gray-500">Complétion du dossier</div>
                <div class="progress-bar"><div class="fill" style="width: {{ $completion }}%;"></div></div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-black" style="color: var(--primary);">{{ $artisan->created_at->diffForHumans() }}</div>
                <div class="text-sm text-gray-500">Membre depuis</div>
            </div>
        </div>

        {{-- GRILLE PRINCIPALE 2 COLONNES --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- 01 – Informations personnelles --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-user-circle"></i>  Informations personnelles</div>
                <div class="info-grid">
                    <div class="info-item"><span class="label">Nom complet</span><span class="value">{{ $artisan->user->first_name }} {{ $artisan->user->last_name }}</span></div>
                    <div class="info-item"><span class="label">Genre</span><span class="value">{{ ['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre'][$artisan->user->gender] ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Date de naissance</span><span class="value">{{ $artisan->user->date_of_birth ? \Carbon\Carbon::parse($artisan->user->date_of_birth)->format('d/m/Y') : 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Téléphone</span><span class="value">{{ $artisan->user->phone_number ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Email</span><span class="value">{{ $artisan->user->email }}</span></div>
                    <div class="info-item"><span class="label">Adresse de résidence</span><span class="value">{{ $artisan->user->address ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Ville</span><span class="value">{{ $artisan->user->city ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Quartier</span><span class="value">{{ $artisan->user->neighborhood ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Région</span><span class="value">{{ $artisan->user->region ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Nationalité</span><span class="value">{{ $artisan->user->nationality ?? 'Non renseignée' }}</span></div>
                </div>
            </div>

            {{-- 02 – Pièce d'identité --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-id-card"></i> Pièce d'identité</div>
                <div class="info-grid">
                    <div class="info-item"><span class="label">Type</span><span class="value">{{ ['carte_nationale' => 'Carte nationale', 'passeport' => 'Passeport', 'permis' => 'Permis'][$artisan->identity_document_type] ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Numéro</span><span class="value">{{ $artisan->identity_document_number ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Date d'expiration</span><span class="value">{{ $artisan->identity_expiration_date ? $artisan->identity_expiration_date->format('d/m/Y') : 'Non renseignée' }}</span></div>
                </div>
                @if($artisan->identity_photo_recto || $artisan->identity_photo_verso || $artisan->identity_selfie)
                    <div class="id-photos">
                        @if($artisan->identity_photo_recto)
                            <figure><img src="{{ asset('storage/' . $artisan->identity_photo_recto) }}" alt="Recto"><figcaption>Recto</figcaption></figure>
                        @endif
                        @if($artisan->identity_photo_verso)
                            <figure><img src="{{ asset('storage/' . $artisan->identity_photo_verso) }}" alt="Verso"><figcaption>Verso</figcaption></figure>
                        @endif
                        @if($artisan->identity_selfie)
                            <figure><img src="{{ asset('storage/' . $artisan->identity_selfie) }}" alt="Selfie"><figcaption>Selfie</figcaption></figure>
                        @endif
                    </div>
                @else
                    <p class="text-gray-400 text-sm mt-2"><i class="fas fa-info-circle"></i> Aucun document téléchargé.</p>
                @endif
            </div>

            {{-- 03 – Informations professionnelles --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-briefcase"></i> Informations professionnelles</div>
                <div class="info-grid">
                    <div class="info-item"><span class="label">Métier principal</span><span class="value">{{ $artisan->main_profession ?? $artisan->profession ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Années d'expérience</span><span class="value">{{ $artisan->years_experience ?? 0 }} ans</span></div>
                    <div class="info-item"><span class="label">Entreprise</span><span class="value">{{ $artisan->company_name ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">N° d'enregistrement</span><span class="value">{{ $artisan->company_registration_number ?? 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Description</span><span class="value">{{ $artisan->description ?? 'Aucune description' }}</span></div>
                </div>
                @if(!empty($artisan->sub_specialties))
                    <div class="mt-2"><span class="text-xs text-gray-400">Sous-spécialités</span><div>@foreach($artisan->sub_specialties as $spec)<span class="tag">{{ $spec }}</span> @endforeach</div></div>
                @endif
                @if(!empty($artisan->diplomas))
                    <div class="mt-2"><span class="text-xs text-gray-400">Diplômes</span><div>@foreach($artisan->diplomas as $dip)<span class="tag">{{ $dip }}</span> @endforeach</div></div>
                @endif
                @if(!empty($artisan->certifications))
                    <div class="mt-2"><span class="text-xs text-gray-400">Certifications</span><div>@foreach($artisan->certifications as $cert)<span class="tag">{{ $cert }}</span> @endforeach</div></div>
                @endif
            </div>

            {{-- 04 – Langues parlées --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-language"></i> Langues parlées</div>
                @if(!empty($artisan->languages))
                    <div>@foreach($artisan->languages as $lang)<span class="tag">{{ $lang }}</span> @endforeach</div>
                @else
                    <p class="text-gray-400 text-sm"><i class="fas fa-info-circle"></i> Non renseignées</p>
                @endif
            </div>

            {{-- 05 – Zone d'intervention --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-map-marker-alt"></i> Zone d'intervention</div>
                <div class="info-grid">
                    <div class="info-item"><span class="label">Région</span><span class="value">{{ $artisan->intervention_region ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Préfecture</span><span class="value">{{ $artisan->intervention_prefecture ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Ville</span><span class="value">{{ $artisan->intervention_city ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Adresse précise</span><span class="value">{{ $artisan->address ?? 'Non renseignée' }}</span></div>
                    <div class="info-item"><span class="label">Rayon d'intervention</span><span class="value">{{ $artisan->max_distance_km ? $artisan->max_distance_km . ' km' : 'Non renseigné' }}</span></div>
                    <div class="info-item"><span class="label">Horaires de disponibilité</span><span class="value">
                        @if($artisan->availability_start_time && $artisan->availability_end_time)
                            {{ $artisan->availability_start_time->format('H:i') }} - {{ $artisan->availability_end_time->format('H:i') }}
                        @else
                            Non renseignés
                        @endif
                    </span></div>
                </div>
                @if($artisan->latitude && $artisan->longitude)
                    <div id="map"></div>
                @else
                    <p class="text-gray-400 text-sm mt-2"><i class="fas fa-info-circle"></i> Aucune coordonnée géographique.</p>
                @endif
            </div>

            {{-- 06 – Paiement Mobile Money --}}
            <div class="card">
                <div class="section-title"><i class="fas fa-money-bill-wave"></i> Paiement Mobile Money</div>
                @if($artisan->mobile_money_number)
                    <div class="info-grid">
                        <div class="info-item"><span class="label">Numéro</span><span class="value">{{ $artisan->mobile_money_number }}</span></div>
                        <div class="info-item"><span class="label">Opérateur</span><span class="value">{{ $artisan->mobile_money_operator === 'moov' ? 'Moov Money' : ($artisan->mobile_money_operator === 'yas' ? 'Yas Money' : 'Non renseigné') }}</span></div>
                    </div>
                @else
                    <p class="text-gray-400 text-sm"><i class="fas fa-info-circle"></i> Aucune information de paiement.</p>
                @endif
            </div>

            {{-- 07 – Photos de réalisations (pleine largeur) --}}
            <div class="card lg:col-span-2">
                <div class="section-title"><i class="fas fa-images"></i> Photos de réalisations</div>
                @if(!empty($artisan->photos))
                    <div class="photo-gallery">
                        @foreach($artisan->photos as $photo)
                            <img src="{{ asset('storage/' . $photo) }}" alt="Réalisation">
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm"><i class="fas fa-info-circle"></i> Aucune photo téléchargée.</p>
                @endif
            </div>

        </div>{{-- fin grid --}}

        {{-- Bouton retour --}}
        <div class="mt-8">
            <a href="{{ route('admin.profils.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

    </div>{{-- container --}}
</div>

{{-- Leaflet (si coordonnées) --}}
@if($artisan->latitude && $artisan->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([{{ $artisan->latitude }}, {{ $artisan->longitude }}], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            L.marker([{{ $artisan->latitude }}, {{ $artisan->longitude }}])
                .addTo(map)
                .bindPopup("{{ $artisan->user->name }}")
                .openPopup();
        });
    </script>
@endif

{{-- Font Awesome (si pas déjà chargé) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@endsection