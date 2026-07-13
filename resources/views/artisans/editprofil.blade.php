@php
    use App\Models\Setting;

    $themeKeys = [
        'color_primary', 'color_primary_dark', 'color_primary_900', 'color_primary_800',
        'color_primary_light', 'color_secondary', 'color_secondary_light',
        'color_bg', 'color_bg_2', 'color_text', 'color_muted',
    ];

    try {
        $theme = Setting::whereIn('key', $themeKeys)->pluck('value', 'key');
    } catch (\Throwable $e) {
        $theme = collect();
    }

    $c = fn ($key, $fallback) => $theme[$key] ?? $fallback;

    $u = auth()->user();

    // Complétion par section, pour la navigation
    $doneInfo   = (bool) ($u->first_name && $u->last_name && $u->city);
    $doneSecu   = (bool) $artisan->identity_document_type;
    $doneMetier = (bool) $artisan->main_profession;
    $doneZone   = (bool) ($artisan->intervention_region && $artisan->max_distance_km);
    $doneLang   = (bool) !empty($artisan->languages);

    // Pourcentage global de complétion du dossier
    $sectionsDone = collect([$doneInfo, $doneSecu, $doneMetier, $doneZone, $doneLang])->filter()->count();
    $completionPercent = (int) round(($sectionsDone / 5) * 100);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl leading-tight" style="font-family:'Poppins',sans-serif; color: {{ $c('color_text', '#2E1065') }};">
                Mon dossier professionnel
            </h2>
            <p class="text-sm mt-1" style="color: {{ $c('color_muted', '#6B5B95') }};">
                Ces informations sont visibles par nos clients et par l'équipe Artilo.
            </p>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            --c-primary: {{ $c('color_primary', '#7C3AED') }};
            --c-primary-dark: {{ $c('color_primary_dark', '#6D28D9') }};
            --c-primary-900: {{ $c('color_primary_900', '#2E1065') }};
            --c-primary-800: {{ $c('color_primary_800', '#4C1D95') }};
            --c-primary-light: {{ $c('color_primary_light', '#A78BFA') }};
            --c-secondary: {{ $c('color_secondary', '#D97706') }};
            --c-secondary-light: {{ $c('color_secondary_light', '#F59E0B') }};
            --c-bg: {{ $c('color_bg', '#FFFFFF') }};
            --c-bg-2: {{ $c('color_bg_2', '#FAF8FF') }};
            --c-text: {{ $c('color_text', '#2E1065') }};
            --c-muted: {{ $c('color_muted', '#6B5B95') }};

            /* Teintes dérivées dynamiquement de la couleur primaire (remplacent les rgba figés) */
            --c-primary-08: color-mix(in srgb, var(--c-primary) 8%, transparent);
            --c-primary-10: color-mix(in srgb, var(--c-primary) 10%, transparent);
            --c-primary-12: color-mix(in srgb, var(--c-primary) 12%, transparent);
            --c-primary-15: color-mix(in srgb, var(--c-primary) 15%, transparent);
            --c-primary-18: color-mix(in srgb, var(--c-primary) 18%, transparent);
            --c-primary-22: color-mix(in srgb, var(--c-primary) 22%, transparent);
            --c-primary-60: color-mix(in srgb, var(--c-primary) 60%, transparent);
            --c-primary-75: color-mix(in srgb, var(--c-primary) 75%, transparent);

            /* Teintes dérivées de la couleur secondaire */
            --c-secondary-15: color-mix(in srgb, var(--c-secondary) 15%, transparent);
            --c-secondary-30: color-mix(in srgb, var(--c-secondary) 30%, transparent);
        }

        .ep-page { font-family: 'Poppins', sans-serif; color: var(--c-text); }
        .ep-page h1, .ep-page h2, .ep-page h3, .ep-page .ep-display { font-family: 'Poppins', sans-serif; font-weight: 700; }
        .ep-mono { font-family: 'Poppins', sans-serif; letter-spacing: .03em; }

        .ep-shell {
            background: radial-gradient(ellipse at top left, var(--c-bg-2) 0%, var(--c-bg) 55%);
        }

        /* ---------- Barre de progression du dossier ---------- */
        .ep-progress-wrap {
            padding: 12px 14px 14px 14px;
            border-bottom: 1px dashed var(--c-primary-18);
            margin-bottom: 8px;
        }
        .ep-progress-label {
            display: flex; justify-content: space-between; align-items: baseline;
            font-size: 11.5px; font-weight: 600; color: var(--c-muted);
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 7px;
        }
        .ep-progress-label b {
            font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 13px; color: var(--c-secondary);
        }
        .ep-progress-track {
            height: 6px; border-radius: 999px; background: var(--c-primary-10);
            overflow: hidden;
        }
        .ep-progress-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--c-secondary), var(--c-secondary-light));
            transition: width .4s ease;
        }

        /* ---------- Navigation laterale ---------- */
        .ep-nav {
            background: var(--c-bg);
            border: 1px solid var(--c-primary-12);
            border-radius: 14px;
        }
        .ep-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 10px;
            font-size: 13.5px; font-weight: 500; color: var(--c-muted);
            text-decoration: none; border-left: 3px solid transparent;
            transition: background .15s ease, color .15s ease;
        }
        .ep-nav a:hover { background: var(--c-bg-2); color: var(--c-text); }
        .ep-nav a.is-active {
            background: var(--c-bg-2); color: var(--c-primary-900);
            border-left-color: var(--c-primary); font-weight: 600;
        }
        .ep-nav .dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
            background: var(--c-primary-15);
        }
        .ep-nav .dot.on { background: var(--c-secondary); }
        .ep-nav .num {
            font-family: 'Poppins', sans-serif; font-weight: 500; font-size: 11px;
            color: var(--c-primary-light); margin-left: auto;
        }

        /* ---------- Fiches (sections) ---------- */
        .ep-fiche {
            background: var(--c-bg);
            border: 1px solid var(--c-primary-15);
            border-radius: 16px; position: relative; overflow: hidden;
            transition: border-color .15s ease;
        }
        .ep-fiche[open] { border-color: var(--c-primary-22); }
        .ep-fiche::before, .ep-fiche::after {
            content: ""; position: absolute; width: 14px; height: 14px; top: 10px;
            border-top: 2px solid var(--c-primary-light); opacity: .55;
        }
        .ep-fiche::before { left: 10px; border-left: 2px solid var(--c-primary-light); }
        .ep-fiche::after { right: 10px; border-right: 2px solid var(--c-primary-light); }

        .ep-fiche summary {
            list-style: none; cursor: pointer; user-select: none;
            display: flex; align-items: center; gap: 14px;
            padding: 20px 24px;
        }
        .ep-fiche summary::-webkit-details-marker { display: none; }
        .ep-fiche .ep-num {
            font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 22px;
            color: var(--c-primary-light); width: 34px; flex-shrink: 0;
        }
        .ep-fiche summary .ep-title { font-size: 17px; font-weight: 600; color: var(--c-text); flex: 1; }
        .ep-fiche summary .ep-status {
            font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
            background: var(--c-primary-10); color: var(--c-muted);
        }
        .ep-fiche summary .ep-status.on {
            background: var(--c-secondary-15); color: var(--c-secondary);
        }
        .ep-fiche summary .ep-chevron {
            width: 20px; height: 20px; transition: transform .2s ease; color: var(--c-primary);
        }
        .ep-fiche[open] summary .ep-chevron { transform: rotate(180deg); }
        .ep-fiche .ep-body { padding: 4px 24px 26px 24px; border-top: 1px dashed var(--c-primary-18); }

        /* ---------- Champs ---------- */
        .ep-field label {
            display: flex; align-items: center; gap: 8px;
            font-size: 12.5px; font-weight: 600; letter-spacing: .02em;
            color: var(--c-muted); text-transform: uppercase; margin-bottom: 6px;
        }
        .ep-optional {
            font-family: 'Poppins', sans-serif; text-transform: none; font-weight: 600;
            font-size: 10.5px; letter-spacing: 0; padding: 2px 8px; border-radius: 999px;
            background: var(--c-secondary-15); color: var(--c-secondary-light);
            filter: brightness(.85);
        }
        .ep-field input[type="text"],
        .ep-field input[type="email"],
        .ep-field input[type="tel"],
        .ep-field input[type="date"],
        .ep-field input[type="time"],
        .ep-field input[type="number"],
        .ep-field select,
        .ep-field textarea {
            width: 100%; padding: 10px 12px; border-radius: 9px;
            border: 1.5px solid var(--c-primary-18); background: var(--c-bg);
            font-family: 'Poppins', sans-serif; font-size: 14.5px; color: var(--c-text);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .ep-field input:focus, .ep-field select:focus, .ep-field textarea:focus {
            outline: none; border-color: var(--c-primary);
            box-shadow: 0 0 0 3px var(--c-primary-15);
        }
        .ep-hint { font-size: 12px; color: var(--c-muted); margin-top: 5px; }

        /* ---------- Upload fichier ---------- */
        .ep-file-btn {
            display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
            padding: 9px 16px; border-radius: 9px; font-size: 13.5px; font-weight: 600;
            background: var(--c-bg-2); color: var(--c-primary-900);
            border: 1.5px dashed var(--c-primary-light);
            transition: background .15s ease, border-color .15s ease;
        }
        .ep-file-btn:hover { background: var(--c-primary-10); border-color: var(--c-secondary); }
        .ep-file-btn input { display: none; }

        /* ---------- Chips langues ---------- */
        .ep-chip {
            display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
            padding: 8px 16px; border-radius: 999px; font-size: 13.5px; font-weight: 500;
            border: 1.5px solid var(--c-primary-22); color: var(--c-muted);
            transition: all .15s ease; user-select: none;
        }
        .ep-chip input { display: none; }
        .ep-chip.is-on {
            background: var(--c-primary); border-color: var(--c-primary); color: white;
        }

        /* ---------- Boutons ---------- */
        .ep-btn-primary {
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            color: white; font-weight: 600; padding: 12px 26px; border-radius: 11px;
            box-shadow: 0 8px 20px -8px var(--c-primary-60);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .ep-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px -8px var(--c-primary-75); }
        .ep-btn-ghost {
            background: var(--c-bg); color: var(--c-muted); font-weight: 600;
            padding: 12px 26px; border-radius: 11px; border: 1.5px solid var(--c-primary-18);
        }
        .ep-btn-ghost:hover { background: var(--c-bg-2); }

        /* ---------- Carte professionnelle (signature) ---------- */
        .ep-card {
            background: linear-gradient(160deg, var(--c-primary-900), var(--c-primary-800));
            border-radius: 20px; padding: 26px; color: white; position: relative; overflow: hidden;
        }
        .ep-card::after {
            content: ""; position: absolute; inset: 0; border-radius: 20px;
            background-image: repeating-linear-gradient(135deg, rgba(255,255,255,0.035) 0 2px, transparent 2px 14px);
            pointer-events: none;
        }
        .ep-card-eyebrow {
            font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 10.5px; letter-spacing: .16em;
            text-transform: uppercase; color: var(--c-primary-light); display: flex; justify-content: space-between;
        }
        .ep-card-photo {
            width: 64px; height: 64px; border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5); background: rgba(255,255,255,0.12);
        }
        .ep-card-avatar-fallback {
            width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.12); border: 2px solid rgba(255,255,255,0.5);
            font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 700;
        }
        .ep-card-name { font-family: 'Poppins', sans-serif; font-size: 21px; font-weight: 700; line-height: 1.15; }
        .ep-card-profession { font-size: 13.5px; color: var(--c-secondary-light); margin-top: 2px; }
        .ep-card-divider { border-top: 1px dashed rgba(255,255,255,0.25); margin: 16px 0; }
        .ep-card-stat { font-size: 12px; color: rgba(255,255,255,0.65); }
        .ep-card-stat b { display: block; font-size: 15px; color: white; font-weight: 600; font-family: 'Poppins', sans-serif; letter-spacing: .02em; }
        .ep-card-badge {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
            padding: 4px 10px; border-radius: 999px; margin-top: 12px;
        }
        .ep-card-badge.on { background: rgba(52,211,153,0.18); color: #6EE7B7; }
        .ep-card-badge.off { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.55); }
        .ep-card-lang {
            font-size: 11px; background: rgba(255,255,255,0.12); padding: 3px 9px;
            border-radius: 999px; margin: 2px 3px 0 0; display: inline-block;
        }

        @media (prefers-reduced-motion: reduce) {
            .ep-btn-primary, .ep-fiche summary .ep-chevron, .ep-progress-fill { transition: none; }
        }
    </style>

    <div class="py-10 ep-shell ep-page">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl" style="background: rgba(52,211,153,0.12); color:#065F46; border:1px solid rgba(52,211,153,0.3);">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl" style="background: rgba(239,68,68,0.08); color:#991B1B; border:1px solid rgba(239,68,68,0.25);">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('artisan.profil.update') }}" method="POST" enctype="multipart/form-data" id="ep-form">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr_300px] gap-6 items-start">

                    <!-- ============ NAVIGATION LATERALE ============ -->
                    <nav class="ep-nav p-3 lg:sticky lg:top-6 hidden lg:flex flex-col gap-1">
                        <div class="ep-progress-wrap">
                            <div class="ep-progress-label">
                                <span>Dossier complété</span>
                                <b>{{ $completionPercent }}%</b>
                            </div>
                            <div class="ep-progress-track">
                                <div class="ep-progress-fill" style="width: {{ $completionPercent }}%;"></div>
                            </div>
                        </div>
                        <a href="#section-perso"><span class="dot {{ $doneInfo ? 'on' : '' }}"></span>Identité<span class="num"></span></a>
                        <a href="#section-securite"><span class="dot {{ $doneSecu ? 'on' : '' }}"></span>Sécurité<span class="num"></span></a>
                        <a href="#section-metier"><span class="dot {{ $doneMetier ? 'on' : '' }}"></span>Métier<span class="num"></span></a>
                        <a href="#section-zone"><span class="dot {{ $doneZone ? 'on' : '' }}"></span>Zone<span class="num"></span></a>
                        <a href="#section-langues"><span class="dot {{ $doneLang ? 'on' : '' }}"></span>Langues<span class="num"></span></a>
                    </nav>

                    <!-- ============ COLONNE FORMULAIRE ============ -->
                    <div class="space-y-5">

                        <!-- 01. INFORMATIONS PERSONNELLES -->
                        <details open class="ep-fiche" id="section-perso">
                            <summary>
                                
                                <span class="ep-title">Informations personnelles</span>
                                <span class="ep-status {{ $doneInfo ? 'on' : '' }}">{{ $doneInfo ? 'Complet' : 'À compléter' }}</span>
                                <svg class="ep-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="ep-body space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="last_name">Nom</label>
                                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $u->last_name) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="first_name">Prénom</label>
                                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $u->first_name) }}">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="gender">Sexe</label>
                                        <select name="gender" id="gender">
                                            <option value="">-- Choisir --</option>
                                            <option value="male" {{ old('gender', $u->gender) == 'male' ? 'selected' : '' }}>Masculin</option>
                                            <option value="female" {{ old('gender', $u->gender) == 'female' ? 'selected' : '' }}>Féminin</option>
                                            <option value="other" {{ old('gender', $u->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                    </div>
                                    <div class="ep-field">
                                        <label for="date_of_birth">Date de naissance</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $u->date_of_birth?->format('Y-m-d')) }}">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label>Photo de profil</label>
                                    <div class="flex items-center gap-4">
                                        <label class="ep-file-btn">
                                             Choisir une photo
                                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*">
                                        </label>
                                        @if($u->profile_photo)
                                            <img src="{{ asset('storage/' . $u->profile_photo) }}" alt="Photo profil" class="w-14 h-14 rounded-full object-cover border" style="border-color: var(--c-primary-light);">
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="phone_number">Numéro de téléphone</label>
                                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $u->phone_number) }}" placeholder="Ex: +228 90 12 34 56">
                                    </div>
                                    <div class="ep-field">
                                        <label for="email">Adresse e-mail</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $u->email) }}">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label for="address">Adresse de résidence</label>
                                    <input type="text" name="address" id="address" value="{{ old('address', $u->address) }}" placeholder="Ex: 123 Rue de la République, Lomé">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="ep-field">
                                        <label for="city">Ville</label>
                                        <input type="text" name="city" id="city" value="{{ old('city', $u->city) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="neighborhood">Quartier</label>
                                        <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $u->neighborhood) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="region">Région</label>
                                        <input type="text" name="region" id="region" value="{{ old('region', $u->region) }}">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label for="nationality">Nationalité</label>
                                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $u->nationality) }}">
                                </div>
                            </div>
                        </details>

                        <!-- 02. SECURITE - PIECE D'IDENTITE -->
                        <details class="ep-fiche" id="section-securite">
                            <summary>
                                
                                <span class="ep-title">Sécurité — Pièce d'identité</span>
                                <span class="ep-status {{ $doneSecu ? 'on' : '' }}">{{ $doneSecu ? 'Complet' : 'À compléter' }}</span>
                                <svg class="ep-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="ep-body space-y-4">

                                <div class="ep-field">
                                    <label for="identity_document_type">Type de pièce d'identité</label>
                                    <select name="identity_document_type" id="identity_document_type">
                                        <option value="">-- Choisir --</option>
                                        <option value="carte_nationale" {{ old('identity_document_type', $artisan->identity_document_type) == 'carte_nationale' ? 'selected' : '' }}>Carte nationale</option>
                                        <option value="passeport" {{ old('identity_document_type', $artisan->identity_document_type) == 'passeport' ? 'selected' : '' }}>Passeport</option>
                                        <option value="permis" {{ old('identity_document_type', $artisan->identity_document_type) == 'permis' ? 'selected' : '' }}>Permis</option>
                                    </select>
                                </div>

                                <div class="ep-field">
                                    <label for="identity_document_number">Numéro de la pièce</label>
                                    <input type="text" name="identity_document_number" id="identity_document_number" class="ep-mono" value="{{ old('identity_document_number', $artisan->identity_document_number) }}">
                                </div>

                                <div class="ep-field">
                                    <label for="identity_expiration_date">Date d'expiration</label>
                                    <input type="date" name="identity_expiration_date" id="identity_expiration_date" value="{{ old('identity_expiration_date', $artisan->identity_expiration_date?->format('Y-m-d')) }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label>Photo recto</label>
                                        <label class="ep-file-btn">
                                            Choisir un fichier
                                            <input type="file" name="identity_photo_recto" accept="image/*">
                                        </label>
                                        @if($artisan->identity_photo_recto)
                                            <img src="{{ asset('storage/' . $artisan->identity_photo_recto) }}" alt="Recto" class="mt-2 w-16 h-16 rounded object-cover border">
                                        @endif
                                    </div>
                                    <div class="ep-field">
                                        <label>Photo verso</label>
                                        <label class="ep-file-btn">
                                             Choisir un fichier
                                            <input type="file" name="identity_photo_verso" accept="image/*">
                                        </label>
                                        @if($artisan->identity_photo_verso)
                                            <img src="{{ asset('storage/' . $artisan->identity_photo_verso) }}" alt="Verso" class="mt-2 w-16 h-16 rounded object-cover border">
                                        @endif
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label>Selfie avec la pièce d'identité</label>
                                    <label class="ep-file-btn">
                                         Choisir un fichier
                                        <input type="file" name="identity_selfie" accept="image/*">
                                    </label>
                                    @if($artisan->identity_selfie)
                                        <img src="{{ asset('storage/' . $artisan->identity_selfie) }}" alt="Selfie" class="mt-2 w-16 h-16 rounded object-cover border">
                                    @endif
                                </div>
                            </div>
                        </details>

                        <!-- 03. INFORMATIONS PROFESSIONNELLES -->
                        <details class="ep-fiche" id="section-metier">
                            <summary>
                                
                                <span class="ep-title">Informations professionnelles</span>
                                <span class="ep-status {{ $doneMetier ? 'on' : '' }}">{{ $doneMetier ? 'Complet' : 'À compléter' }}</span>
                                <svg class="ep-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="ep-body space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="main_profession">Métier principal</label>
                                        <input type="text" name="main_profession" id="main_profession" value="{{ old('main_profession', $artisan->main_profession) }}" placeholder="Ex: Électricien">
                                    </div>
                                    <div class="ep-field">
                                        <label for="years_experience">Années d'expérience</label>
                                        <input type="number" name="years_experience" id="years_experience" value="{{ old('years_experience', $artisan->years_experience) }}" min="0" max="70">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label for="sub_specialties_text">Sous-spécialités <span class="ep-optional">optionnel</span></label>
                                    <textarea name="sub_specialties_text" id="sub_specialties_text" rows="2" placeholder="Ex: Installation, Réparation, Maintenance (une par ligne)">{{ is_array(old('sub_specialties', $artisan->sub_specialties)) ? implode("\n", $artisan->sub_specialties ?? []) : '' }}</textarea>
                                    <p class="ep-hint">Entrez une spécialité par ligne</p>
                                </div>

                                <div class="ep-field">
                                    <label for="description">Description du métier</label>
                                    <textarea name="description" id="description" rows="4" placeholder="Décrivez vos services, parcours, spécialités...">{{ old('description', $artisan->description) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="company_name">Entreprise <span class="ep-optional">optionnel</span></label>
                                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $artisan->company_name) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="company_registration_number">Numéro d'enregistrement <span class="ep-optional">optionnel</span></label>
                                        <input type="text" name="company_registration_number" id="company_registration_number" class="ep-mono" value="{{ old('company_registration_number', $artisan->company_registration_number) }}">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label for="diplomas_text">Diplômes</label>
                                    <textarea name="diplomas_text" id="diplomas_text" rows="2" placeholder="Ex: BTS Électricité, Licence Ingénierie (une par ligne)">{{ is_array(old('diplomas', $artisan->diplomas)) ? implode("\n", $artisan->diplomas ?? []) : '' }}</textarea>
                                    <p class="ep-hint">Entrez un diplôme par ligne</p>
                                </div>

                                <div class="ep-field">
                                    <label for="certifications_text">Certifications</label>
                                    <textarea name="certifications_text" id="certifications_text" rows="2" placeholder="Ex: Certification ISO, Agrément COTOCO (une par ligne)">{{ is_array(old('certifications', $artisan->certifications)) ? implode("\n", $artisan->certifications ?? []) : '' }}</textarea>
                                    <p class="ep-hint">Entrez une certification par ligne</p>
                                </div>

                                <div class="ep-field">
                                    <label>Photos de vos réalisations</label>
                                    <label class="ep-file-btn">
                                         Choisir des fichiers
                                        <input type="file" name="photos[]" accept="image/*" multiple>
                                    </label>
                                    @if(!empty($artisan->photos))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($artisan->photos as $photo)
                                                <img src="{{ asset('storage/' . $photo) }}" alt="Réalisation" class="w-16 h-16 rounded object-cover border">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </details>

                        <!-- 04. ZONE D'INTERVENTION -->
                        <details class="ep-fiche" id="section-zone">
                            <summary>
                                
                                <span class="ep-title">Zone d'intervention</span>
                                <span class="ep-status {{ $doneZone ? 'on' : '' }}">{{ $doneZone ? 'Complet' : 'À compléter' }}</span>
                                <svg class="ep-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="ep-body space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="ep-field">
                                        <label for="intervention_region">Région</label>
                                        <input type="text" name="intervention_region" id="intervention_region" value="{{ old('intervention_region', $artisan->intervention_region) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="intervention_prefecture">Préfecture</label>
                                        <input type="text" name="intervention_prefecture" id="intervention_prefecture" value="{{ old('intervention_prefecture', $artisan->intervention_prefecture) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="intervention_city">Ville</label>
                                        <input type="text" name="intervention_city" id="intervention_city" value="{{ old('intervention_city', $artisan->intervention_city) }}">
                                    </div>
                                </div>

                                <div class="ep-field">
                                    <label for="max_distance_km">Rayon d'intervention maximal</label>
                                    <select name="max_distance_km" id="max_distance_km">
                                        <option value="">-- Choisir --</option>
                                        <option value="5" {{ old('max_distance_km', $artisan->max_distance_km) == '5' ? 'selected' : '' }}>5 km</option>
                                        <option value="10" {{ old('max_distance_km', $artisan->max_distance_km) == '10' ? 'selected' : '' }}>10 km</option>
                                        <option value="20" {{ old('max_distance_km', $artisan->max_distance_km) == '20' ? 'selected' : '' }}>20 km</option>
                                        <option value="50" {{ old('max_distance_km', $artisan->max_distance_km) == '50' ? 'selected' : '' }}>50 km</option>
                                        <option value="100" {{ old('max_distance_km', $artisan->max_distance_km) == '100' ? 'selected' : '' }}>Toute la ville</option>
                                    </select>
                                </div>

                                <div class="ep-field">
                                    <label for="artisan_address">Adresse précise + Géolocalisation</label>
                                    <input type="text" name="artisan_address" id="artisan_address" value="{{ old('artisan_address', $artisan->address) }}" placeholder="Ex: 123 Rue de la République, Lomé">
                                    <button type="button" id="detect-location" class="ep-btn-ghost mt-2" style="padding:8px 16px; font-size:13px;"> Utiliser ma position actuelle</button>
                                    <p id="location-status" class="ep-hint">
                                        @if($artisan->latitude && $artisan->longitude)
                                            Position enregistrée : {{ $artisan->latitude }}, {{ $artisan->longitude }}
                                        @endif
                                    </p>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $artisan->latitude) }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $artisan->longitude) }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="availability_start_time">Horaire de début</label>
                                        <input type="time" name="availability_start_time" id="availability_start_time" value="{{ old('availability_start_time', $artisan->availability_start_time?->format('H:i')) }}">
                                    </div>
                                    <div class="ep-field">
                                        <label for="availability_end_time">Horaire de fin</label>
                                        <input type="time" name="availability_end_time" id="availability_end_time" value="{{ old('availability_end_time', $artisan->availability_end_time?->format('H:i')) }}">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="ep-field">
                                        <label for="mobile_money_number">Numéro Mobile Money</label>
                                        <input type="text" name="mobile_money_number" id="mobile_money_number" class="ep-mono" value="{{ old('mobile_money_number', $artisan->mobile_money_number) }}" placeholder="Ex: 90 12 34 56">
                                    </div>
                                    <div class="ep-field">
                                        <label for="mobile_money_operator">Opérateur</label>
                                        <select name="mobile_money_operator" id="mobile_money_operator">
                                            <option value="">-- Choisir --</option>
                                            <option value="moov" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'moov' ? 'selected' : '' }}>Moov Money</option>
                                            <option value="yas" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'yas' ? 'selected' : '' }}>Yas Money</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </details>

                        <!-- 05. LANGUES PARLEES -->
                        <details class="ep-fiche" id="section-langues">
                            <summary>
                                
                                <span class="ep-title">Langues parlées</span>
                                <span class="ep-status {{ $doneLang ? 'on' : '' }}">{{ $doneLang ? 'Complet' : 'À compléter' }}</span>
                                <svg class="ep-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="ep-body">
                                <div class="flex flex-wrap gap-2" id="lang-chips">
                                    @foreach(['Français','Éwé','Mina','Kabyè','Anglais'] as $lang)
                                        <label class="ep-chip {{ in_array($lang, $artisan->languages ?? []) ? 'is-on' : '' }}">
                                            <input type="checkbox" name="languages[]" value="{{ $lang }}" {{ in_array($lang, $artisan->languages ?? []) ? 'checked' : '' }}>
                                            {{ $lang }}
                                        </label>
                                    @endforeach
                                </div>

                                <div class="ep-field mt-4">
                                    <label for="other_languages">Autres langues <span class="ep-optional">optionnel</span></label>
                                    <input type="text" name="other_languages" id="other_languages" placeholder="Ex: Arabe, Portugais...">
                                </div>
                            </div>
                        </details>

                        <!-- SUBMIT -->
                        <div class="flex gap-4 pt-2 pb-10">
                            <button type="submit" class="ep-btn-primary"> Mettre à jour le profil</button>
                            <a href="{{ route('artisan.MaPage') }}" class="ep-btn-ghost">Annuler</a>
                        </div>
                    </div>

                    <!-- ============ CARTE PROFESSIONNELLE (APERCU LIVE) ============ -->
                    <div class="lg:sticky lg:top-6">
                        <div class="ep-card">
                            <div class="ep-card-eyebrow">
                                <span>ARTILO</span>
                                <span>CARTE PRO</span>
                            </div>

                            <div class="flex items-center gap-3 mt-4">
                                @if($u->profile_photo)
                                    <img src="{{ asset('storage/' . $u->profile_photo) }}" class="ep-card-photo" id="preview-photo-img">
                                @else
                                    <div class="ep-card-avatar-fallback" id="preview-avatar-fallback">
                                        {{ strtoupper(substr($u->first_name ?? $u->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <img src="" class="ep-card-photo hidden" id="preview-photo-img">
                                @endif
                                <div>
                                    <div class="ep-card-name" id="preview-name">{{ trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: 'Votre nom' }}</div>
                                    <div class="ep-card-profession" id="preview-profession">{{ $artisan->main_profession ?? 'Votre métier' }}</div>
                                </div>
                            </div>

                            <div class="ep-card-divider"></div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="ep-card-stat">Expérience<b id="preview-years">{{ $artisan->years_experience ?? 0 }} ans</b></div>
                                <div class="ep-card-stat">Rayon<b id="preview-distance">{{ $artisan->max_distance_km ? $artisan->max_distance_km.' km' : '—' }}</b></div>
                                <div class="ep-card-stat">Note<b>{{ $artisan->average_rating ?? '—' }}</b></div>
                            </div>

                            <div id="preview-availability">
                                @if($artisan->availability_start_time && $artisan->availability_end_time)
                                    <span class="ep-card-badge on">● Disponible {{ $artisan->availability_start_time->format('H:i') }}–{{ $artisan->availability_end_time->format('H:i') }}</span>
                                @else
                                    <span class="ep-card-badge off">● Horaires non renseignés</span>
                                @endif
                            </div>

                            <div class="ep-card-divider"></div>

                            <div class="ep-card-stat" id="preview-city"> {{ $u->city ?? $artisan->intervention_region ?? 'Localisation non renseignée' }}</div>

                            <div class="mt-3" id="preview-languages">
                                @forelse($artisan->languages ?? [] as $lang)
                                    <span class="ep-card-lang">{{ $lang }}</span>
                                @empty
                                    <span class="ep-card-lang">Langues non renseignées</span>
                                @endforelse
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Détection de position
        document.getElementById('detect-location').addEventListener('click', function () {
            const status = document.getElementById('location-status');
            if (!navigator.geolocation) {
                status.textContent = "La géolocalisation n'est pas supportée par votre navigateur.";
                return;
            }
            status.textContent = "Détection en cours...";
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    status.textContent = "Position détectée avec succès ✅ (" + position.coords.latitude.toFixed(4) + ", " + position.coords.longitude.toFixed(4) + ")";
                },
                function () {
                    status.textContent = "Impossible de récupérer votre position. Vérifiez que la localisation est activée.";
                }
            );
        });

        // Chips langues : bascule visuelle au clic
        document.querySelectorAll('.ep-chip').forEach(function (chip) {
            const input = chip.querySelector('input');
            chip.addEventListener('click', function (e) {
                e.preventDefault();
                input.checked = !input.checked;
                chip.classList.toggle('is-on', input.checked);
                updateLanguagesPreview();
            });
        });

        function updateLanguagesPreview() {
            const checked = Array.from(document.querySelectorAll('#lang-chips input:checked')).map(i => i.value);
            const wrap = document.getElementById('preview-languages');
            wrap.innerHTML = checked.length
                ? checked.map(l => '<span class="ep-card-lang">' + l + '</span>').join('')
                : '<span class="ep-card-lang">Langues non renseignées</span>';
        }

        // Métier principal -> aperçu
        document.getElementById('main_profession')?.addEventListener('input', function () {
            document.getElementById('preview-profession').textContent = this.value || 'Votre métier';
        });

        // Région d'intervention -> aperçu localisation
        document.getElementById('intervention_region')?.addEventListener('input', function () {
            document.getElementById('preview-city').textContent = '📍 ' + (this.value || 'Localisation non renseignée');
        });

        // Nom complet
        function updateNamePreview() {
            const first = document.getElementById('first_name')?.value || '';
            const last = document.getElementById('last_name')?.value || '';
            const full = (first + ' ' + last).trim();
            const nameEl = document.getElementById('preview-name');
            if (nameEl) nameEl.textContent = full || 'Votre nom';
            const fallback = document.getElementById('preview-avatar-fallback');
            if (fallback) fallback.textContent = (first || 'A').charAt(0).toUpperCase();
        }
        document.getElementById('first_name')?.addEventListener('input', updateNamePreview);
        document.getElementById('last_name')?.addEventListener('input', updateNamePreview);

        // Années d'expérience
        document.getElementById('years_experience')?.addEventListener('input', function () {
            document.getElementById('preview-years').textContent = (this.value || '0') + ' ans';
        });

        // Rayon d'intervention
        document.getElementById('max_distance_km')?.addEventListener('change', function () {
            document.getElementById('preview-distance').textContent = this.value ? this.value + ' km' : '—';
        });

        // Disponibilité
        function updateAvailabilityPreview() {
            const start = document.getElementById('availability_start_time')?.value;
            const end = document.getElementById('availability_end_time')?.value;
            const wrap = document.getElementById('preview-availability');
            if (start && end) {
                wrap.innerHTML = '<span class="ep-card-badge on">● Disponible ' + start + '–' + end + '</span>';
            } else {
                wrap.innerHTML = '<span class="ep-card-badge off">● Horaires non renseignés</span>';
            }
        }
        document.getElementById('availability_start_time')?.addEventListener('input', updateAvailabilityPreview);
        document.getElementById('availability_end_time')?.addEventListener('input', updateAvailabilityPreview);

        // Photo de profil : aperçu instantané
        document.getElementById('profile_photo')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (evt) {
                const img = document.getElementById('preview-photo-img');
                const fallback = document.getElementById('preview-avatar-fallback');
                img.src = evt.target.result;
                img.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        // Navigation laterale : surbrillance de la section visible
        const navLinks = document.querySelectorAll('.ep-nav a');
        const sections = Array.from(navLinks).map(a => document.querySelector(a.getAttribute('href')));
        function highlightNav() {
            let current = sections[0];
            sections.forEach(sec => {
                if (sec && sec.getBoundingClientRect().top < 140) current = sec;
            });
            navLinks.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + current?.id));
        }
        window.addEventListener('scroll', highlightNav);
        highlightNav();

        // Ouvre automatiquement une fiche quand on clique dessus depuis la nav
       navLinks.forEach(a => {
    a.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const target = document.querySelector(targetId);
        if (target) {
            if (!target.open) target.open = true;
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.replaceState(null, '', targetId);
        }
    });
});
    </script>
</x-app-layout>
