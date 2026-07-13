@php
    // Récupérer les couleurs depuis les settings
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

    $color_primary = $settings['color_primary'] ?? '#7C3AED';
    $color_primary_dark = $settings['color_primary_dark'] ?? '#6D28D9';
    $color_primary_light = $settings['color_primary_light'] ?? '#A78BFA';
    $color_secondary = $settings['color_secondary'] ?? '#D97706';
    $color_secondary_light = $settings['color_secondary_light'] ?? '#F59E0B';
    $color_bg = $settings['color_bg'] ?? '#FFFFFF';
    $color_text = $settings['color_text'] ?? '#2E1065';
    $color_muted = $settings['color_muted'] ?? '#6B5B95';
@endphp

<x-app-layout>
    {{-- Styles personnalisés avec effet vitré (glassmorphism) --}}
    <style>
        .demande-page {
            --primary: {{ $color_primary }};
            --primary-dark: {{ $color_primary_dark }};
            --primary-light: {{ $color_primary_light }};
            --secondary: {{ $color_secondary }};
            --secondary-light: {{ $color_secondary_light }};
            --bg: {{ $color_bg }};
            --text: {{ $color_text }};
            --muted: {{ $color_muted }};

            position: relative;
            min-height: 100vh;
            padding: 3rem 1rem;
            /* Image de fond du bâtiment */
            background-image: url('{{ asset('images/batiment.jfif') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Voile dégradé par-dessus l'image pour la lisibilité */
        /* Aucun voile sur l'image de fond */
.demande-page::before {
    content: none;
}

        .demande-page > * {
            position: relative;
            z-index: 1;
        }

        /* ===== CARTE VITRÉE (glassmorphism) ===== */
        .demande-page .card {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(18px) saturate(140%);
            -webkit-backdrop-filter: blur(18px) saturate(140%);
            transition: all 0.3s ease;
        }

        .demande-page .card:hover {
            box-shadow: 0 12px 50px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Textes clairs sur fond vitré */
        .demande-page .section-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.6rem;
            text-shadow: 0 1px 8px rgba(0, 0, 0, 0.2);
        }

        .demande-page .section-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
        }

        .demande-page .form-label {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .demande-page h4,
        .demande-page label {
            color: #ffffff !important;
        }

        /* Champs translucides */
        .demande-page .form-control {
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .demande-page .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .demande-page .form-control option {
            color: #2E1065;
        }

        .demande-page .form-control:focus {
            border-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.22);
            outline: none;
        }

        .demande-page .form-control.error {
            border-color: #fca5a5;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        /* Bouton principal dégradé */
        .demande-page .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary-light), var(--secondary));
            color: #fff;
            font-weight: 700;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(217, 119, 6, 0.4);
        }

        .demande-page .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.5);
        }

        .demande-page .btn-primary-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .demande-page .btn-ghost {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .demande-page .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .demande-page .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .demande-page .badge-primary {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Zone d'upload vitrée */
        .demande-page .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.4);
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.08);
        }

        .demande-page .upload-zone:hover,
        .demande-page .upload-zone.dragover {
            border-color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.16);
        }

        .demande-page .upload-zone svg {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }

        .demande-page .upload-zone p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
        }

        .demande-page .upload-zone span {
            color: #ffffff;
            font-weight: 600;
            text-decoration: underline;
        }

        .demande-page .upload-zone small {
            color: rgba(255, 255, 255, 0.6);
        }

        .demande-page .photo-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .demande-page .photo-preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .demande-page .photo-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .demande-page .photo-preview-item .remove-photo {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 24px;
            height: 24px;
            border-radius: 9999px;
            background: #ef4444;
            color: #fff;
            border: none;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .demande-page .photo-preview-item .remove-photo:hover {
            transform: scale(1.1);
        }

        /* Carte localisation vitrée */
        .demande-page .location-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .demande-page .error-message {
            color: #fca5a5;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .demande-page .success-message {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #d1fae5;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
        }

        .demande-page .info-message {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #dbeafe;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
        }

        .demande-page .error-container {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fee2e2;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
        }

        /* Indicateur d'étape */
        .demande-page .step-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .demande-page .step-indicator .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
        }

        .demande-page .step-indicator .step.active {
            color: #ffffff;
        }

        .demande-page .step-indicator .step-number {
            width: 28px;
            height: 28px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
        }

        .demande-page .step-indicator .step.active .step-number {
            background: #ffffff;
            color: var(--primary);
        }

        .demande-page .step-indicator .line {
            flex: 1;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 640px) {
            .demande-page .step-indicator {
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .demande-page .step-indicator .line {
                display: none;
            }

            .demande-page .photo-preview-grid {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            }
        }
    </style>

    <div class="demande-page">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6 lg:p-8">

                {{-- En-tête avec indicateur d'étape --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="section-title">Nouvelle demande de service</h2>
                        <span class="badge badge-primary">Étape 1 sur 2</span>
                    </div>
                    <p class="section-subtitle">Remplissez les informations ci-dessous pour lancer votre recherche d'artisan.</p>

                    {{-- Indicateur d'étape --}}
                    <div class="step-indicator mt-4">
                        <div class="step active">
                            <span class="step-number">1</span>
                            Informations
                        </div>
                        <div class="line"></div>
                        <div class="step">
                            <span class="step-number">2</span>
                            Suivi
                        </div>
                    </div>
                </div>

                {{-- Messages flash --}}
                @if(session('success'))
                    <div class="success-message mb-4 flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="font-bold text-xl">&times;</button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="info-message mb-4 flex items-center justify-between">
                        <span>{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="font-bold text-xl">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-container mb-4">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('particulier.demande.store') }}"
                      enctype="multipart/form-data" id="demandeForm">
                    @csrf

                    {{-- Métier --}}
                    <div class="mb-5">
                        <label for="metier_requis" class="form-label block mb-1.5">
                            Métier requis <span class="text-red-300">*</span>
                        </label>
                        <select name="metier_requis" id="metier_requis"
                                class="form-control w-full {{ $errors->has('metier_requis') ? 'error' : '' }}"
                                required>
                            <option value="">Sélectionnez un métier</option>
                            @foreach($metiers as $metier)
                                <option value="{{ $metier }}"
                                    {{ old('metier_requis') == $metier ? 'selected' : '' }}>
                                    {{ $metier }}
                                </option>
                            @endforeach
                        </select>
                        @error('metier_requis')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <label for="description" class="form-label block mb-1.5">
                            Description des travaux <span class="text-red-300">*</span>
                        </label>
                        <textarea name="description" id="description" rows="5"
                                  class="form-control w-full {{ $errors->has('description') ? 'error' : '' }}"
                                  placeholder="Décrivez précisément les travaux à réaliser (ex: réparer une fuite d'eau, installation électrique, etc.)"
                                  required>{{ old('description') }}</textarea>
                        <div class="flex justify-between text-xs mt-1" style="color: rgba(255,255,255,0.6);">
                            <span>Maximum 1000 caractères</span>
                            <span id="charCount">0 / 1000</span>
                        </div>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Date souhaitée --}}
                    <div class="mb-5">
                        <label for="date_souhaitee" class="form-label block mb-1.5">
                            Date souhaitée <span class="text-sm" style="color: rgba(255,255,255,0.6);">(optionnelle)</span>
                        </label>
                        <input type="date" name="date_souhaitee" id="date_souhaitee"
                               value="{{ old('date_souhaitee') }}"
                               min="{{ date('Y-m-d') }}"
                               class="form-control w-full {{ $errors->has('date_souhaitee') ? 'error' : '' }}">
                        @error('date_souhaitee')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Photos avec zone de dépôt --}}
                    <div class="mb-5">
                        <label for="photos" class="form-label block mb-1.5">
                            Photos <span class="text-sm" style="color: rgba(255,255,255,0.6);">(optionnelles, max 5)</span>
                        </label>

                        <div class="upload-zone" id="uploadZone">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>
                                <span>Cliquez pour télécharger</span> ou glissez-déposez vos photos ici
                            </p>
                            <small>JPG, PNG, GIF jusqu'à 5MB</small>
                        </div>

                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="hidden">

                        <div id="photoPreview" class="photo-preview-grid"></div>

                        @error('photos.*')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Localisation --}}
                    <div class="location-card mb-5">
                        <h4 class="font-semibold mb-3">Localisation</h4>

                        <div class="mb-3">
                            <button type="button" onclick="localisermoi()"
                                    class="inline-flex items-center px-4 py-2 rounded-lg transition text-sm font-semibold"
                                    style="background: rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.3);">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Me localiser automatiquement
                            </button>
                            <span id="locatingStatus" class="text-sm ml-2 hidden" style="color: rgba(255,255,255,0.7);">Localisation en cours...</span>
                        </div>

                        <div>
                            <label for="adresse" class="block text-sm font-medium mb-1">
                                Adresse <span class="text-red-300">*</span>
                            </label>
                            <input type="text" name="adresse" id="adresse"
                                   value="{{ old('adresse') }}"
                                   class="form-control w-full {{ $errors->has('adresse') ? 'error' : '' }}"
                                   placeholder="Ex: 123 Rue des Artisans, Lomé"
                                   required>
                            @error('adresse')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                        <div class="text-xs mt-2" id="coordInfo" style="color: rgba(255,255,255,0.6);">
                            @if(old('latitude') && old('longitude'))
                                Coordonnées : {{ old('latitude') }}, {{ old('longitude') }}
                            @else
                                Aucune coordonnée saisie
                            @endif
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-6 pt-4" style="border-top: 1px solid rgba(255,255,255,0.2);">
                        <a href="{{ route('particulier.dashboard') }}"
                           class="btn-ghost w-full sm:w-auto text-center">
                            ← Annuler
                        </a>
                        <button type="submit" id="submitBtn"
                                class="btn-primary-custom w-full sm:w-auto">
                            Envoyer la demande
                            <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7M21 12H3"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // ===== Prévisualisation des photos avec Drag & Drop =====
        (function() {
            const input = document.getElementById('photos');
            const preview = document.getElementById('photoPreview');
            const zone = document.getElementById('uploadZone');
            const MAX_PHOTOS = 5;
            let photoFiles = [];

            function updatePreview() {
                preview.innerHTML = '';
                photoFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function() {
                        const div = document.createElement('div');
                        div.className = 'photo-preview-item';
                        div.innerHTML = `
                            <img src="${reader.result}" alt="Photo ${index + 1}">
                            <button class="remove-photo" data-index="${index}" type="button">×</button>
                        `;
                        preview.appendChild(div);

                        div.querySelector('.remove-photo').addEventListener('click', function(e) {
                            e.stopPropagation();
                            photoFiles.splice(index, 1);
                            updatePreview();
                            updateInput();
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            function updateInput() {
                const dt = new DataTransfer();
                photoFiles.forEach(file => dt.items.add(file));
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            }

            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            zone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                if (photoFiles.length + files.length > MAX_PHOTOS) {
                    alert(`Vous ne pouvez télécharger que ${MAX_PHOTOS} photos maximum.`);
                    return;
                }
                photoFiles = photoFiles.concat(files);
                updatePreview();
                updateInput();
            });

            zone.addEventListener('click', function() {
                input.click();
            });

            input.addEventListener('change', function() {
                const files = Array.from(this.files);
                if (files.length > MAX_PHOTOS) {
                    alert(`Vous ne pouvez télécharger que ${MAX_PHOTOS} photos maximum.`);
                    return;
                }
                photoFiles = files;
                updatePreview();
            });
        })();

        // ===== Compteur de caractères =====
        const description = document.getElementById('description');
        const charCount = document.getElementById('charCount');

        description.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = `${count} / 1000`;
            if (count > 900) {
                charCount.style.color = '#fca5a5';
            } else {
                charCount.style.color = 'rgba(255,255,255,0.6)';
            }
        });
        description.dispatchEvent(new Event('input'));

        // ===== Géolocalisation =====
        function localisermoi() {
            if (navigator.geolocation) {
                const btn = event.target.closest('button');
                const status = document.getElementById('locatingStatus');
                btn.disabled = true;
                status.classList.remove('hidden');
                btn.innerHTML = 'Localisation en cours...';

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        document.getElementById('latitude').value = pos.coords.latitude;
                        document.getElementById('longitude').value = pos.coords.longitude;
                        document.getElementById('coordInfo').textContent =
                            `Coordonnées : ${pos.coords.latitude}, ${pos.coords.longitude}`;

                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json&zoom=18&accept-language=fr`)
                            .then(r => r.json())
                            .then(data => {
                                if (data.display_name) {
                                    document.getElementById('adresse').value = data.display_name;
                                }
                            })
                            .catch(() => {})
                            .finally(() => {
                                btn.innerHTML = 'Me localiser automatiquement';
                                btn.disabled = false;
                                status.classList.add('hidden');
                            });
                    },
                    function(error) {
                        alert('Impossible de récupérer votre position. Veuillez entrer votre adresse manuellement.');
                        btn.innerHTML = 'Me localiser automatiquement';
                        btn.disabled = false;
                        status.classList.add('hidden');
                        console.error('Erreur de géolocalisation:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('La géolocalisation n\'est pas supportée par votre navigateur.');
            }
        }

        // ===== Protection contre les doubles soumissions =====
        document.getElementById('demandeForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Envoi en cours...';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 10000);
        });
    </script>
    <script>
    // ===== Rafraîchissement automatique du token CSRF =====
    // Empêche l'erreur 419 si l'utilisateur met du temps à remplir le formulaire
    setInterval(function() {
        fetch("{{ route('particulier.csrf-refresh') }}")
            .then(response => response.json())
            .then(data => {
                document.querySelector('input[name="_token"]').value = data.token;
            })
            .catch(() => {
                // Silencieux : si ça échoue, le formulaire garde son ancien token
            });
    }, 5 * 60 * 1000); // toutes les 5 minutes
</script>
</x-app-layout>