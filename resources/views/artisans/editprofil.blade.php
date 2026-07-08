<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil Artisan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('artisan.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- 1. INFORMATIONS PERSONNELLES -->
                        <details open class="border rounded-lg p-4 bg-gray-50">
                            <summary class="font-bold text-lg cursor-pointer select-none">👤 Informations personnelles</summary>
                            <div class="mt-4 space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                                        <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                                        <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Sexe</label>
                                        <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                            <option value="">-- Choisir --</option>
                                            <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Masculin</option>
                                            <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Féminin</option>
                                            <option value="other" {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                                    <input type="file" name="profile_photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @if(auth()->user()->profile_photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Photo profil" class="w-20 h-20 rounded-lg object-cover">
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Numéro de téléphone</label>
                                        <input type="tel" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="Ex: +228 90 12 34 56" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Adresse de résidence</label>
                                    <input type="text" name="address" value="{{ old('address', auth()->user()->address) }}" placeholder="Ex: 123 Rue de la République, Lomé" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Ville</label>
                                        <input type="text" name="city" value="{{ old('city', auth()->user()->city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quartier</label>
                                        <input type="text" name="neighborhood" value="{{ old('neighborhood', auth()->user()->neighborhood) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Région</label>
                                        <input type="text" name="region" value="{{ old('region', auth()->user()->region) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nationalité</label>
                                    <input type="text" name="nationality" value="{{ old('nationality', auth()->user()->nationality) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                </div>
                            </div>
                        </details>

                        <!-- 2. SÉCURITÉ - PIÈCE D'IDENTITÉ -->
                        <details class="border rounded-lg p-4 bg-gray-50">
                            <summary class="font-bold text-lg cursor-pointer select-none">🔒 Sécurité - Pièce d'identité</summary>
                            <div class="mt-4 space-y-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Type de pièce d'identité</label>
                                    <select name="identity_document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                        <option value="">-- Choisir --</option>
                                        <option value="carte_nationale" {{ old('identity_document_type', $artisan->identity_document_type) == 'carte_nationale' ? 'selected' : '' }}>Carte nationale</option>
                                        <option value="passeport" {{ old('identity_document_type', $artisan->identity_document_type) == 'passeport' ? 'selected' : '' }}>Passeport</option>
                                        <option value="permis" {{ old('identity_document_type', $artisan->identity_document_type) == 'permis' ? 'selected' : '' }}>Permis</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Numéro de la pièce</label>
                                    <input type="text" name="identity_document_number" value="{{ old('identity_document_number', $artisan->identity_document_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                                    <input type="date" name="identity_expiration_date" value="{{ old('identity_expiration_date', $artisan->identity_expiration_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Photo recto</label>
                                        <input type="file" name="identity_photo_recto" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        @if($artisan->identity_photo_recto)
                                            <img src="{{ asset('storage/' . $artisan->identity_photo_recto) }}" alt="Recto" class="mt-2 w-20 h-20 rounded object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Photo verso</label>
                                        <input type="file" name="identity_photo_verso" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        @if($artisan->identity_photo_verso)
                                            <img src="{{ asset('storage/' . $artisan->identity_photo_verso) }}" alt="Verso" class="mt-2 w-20 h-20 rounded object-cover">
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Selfie avec la pièce d'identité</label>
                                    <input type="file" name="identity_selfie" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @if($artisan->identity_selfie)
                                        <img src="{{ asset('storage/' . $artisan->identity_selfie) }}" alt="Selfie" class="mt-2 w-20 h-20 rounded object-cover">
                                    @endif
                                </div>
                            </div>
                        </details>

                        <!-- 3. INFORMATIONS PROFESSIONNELLES -->
                        <details class="border rounded-lg p-4 bg-gray-50">
                            <summary class="font-bold text-lg cursor-pointer select-none">💼 Informations professionnelles</summary>
                            <div class="mt-4 space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Métier principal</label>
                                        <input type="text" name="main_profession" value="{{ old('main_profession', $artisan->main_profession) }}" placeholder="Ex: Électricien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Années d'expérience</label>
                                        <input type="number" name="years_experience" value="{{ old('years_experience', $artisan->years_experience) }}" min="0" max="70" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sous-spécialités (optionnel)</label>
                                    <textarea name="sub_specialties_text" rows="2" placeholder="Ex: Installation, Réparation, Maintenance (une par ligne)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ is_array(old('sub_specialties', $artisan->sub_specialties)) ? implode("\n", $artisan->sub_specialties ?? []) : '' }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Entrez une spécialité par ligne</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description du métier</label>
                                    <textarea name="description" rows="4" placeholder="Décrivez vos services, parcours, spécialités..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ old('description', $artisan->description) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Entreprise (optionnel)</label>
                                        <input type="text" name="company_name" value="{{ old('company_name', $artisan->company_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Numéro d'enregistrement (optionnel)</label>
                                        <input type="text" name="company_registration_number" value="{{ old('company_registration_number', $artisan->company_registration_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Diplômes</label>
                                    <textarea name="diplomas_text" rows="2" placeholder="Ex: BTS Électricité, Licence Ingénierie (une par ligne)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ is_array(old('diplomas', $artisan->diplomas)) ? implode("\n", $artisan->diplomas ?? []) : '' }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Entrez un diplôme par ligne</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Certifications</label>
                                    <textarea name="certifications_text" rows="2" placeholder="Ex: Certification ISO, Agrément COTOCO (une par ligne)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ is_array(old('certifications', $artisan->certifications)) ? implode("\n", $artisan->certifications ?? []) : '' }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Entrez une certification par ligne</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Photos de vos réalisations</label>
                                    <input type="file" name="photos[]" accept="image/*" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @if(!empty($artisan->photos))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($artisan->photos as $photo)
                                                <img src="{{ asset('storage/' . $photo) }}" alt="Réalisation" class="w-20 h-20 rounded object-cover border">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </details>

                        <!-- 4. ZONE D'INTERVENTION -->
                        <details class="border rounded-lg p-4 bg-gray-50">
                            <summary class="font-bold text-lg cursor-pointer select-none">📍 Zone d'intervention</summary>
                            <div class="mt-4 space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Région</label>
                                        <input type="text" name="intervention_region" value="{{ old('intervention_region', $artisan->intervention_region) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Préfecture</label>
                                        <input type="text" name="intervention_prefecture" value="{{ old('intervention_prefecture', $artisan->intervention_prefecture) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Ville</label>
                                        <input type="text" name="intervention_city" value="{{ old('intervention_city', $artisan->intervention_city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rayon d'intervention maximal (km)</label>
                                    <select name="max_distance_km" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                        <option value="">-- Choisir --</option>
                                        <option value="5" {{ old('max_distance_km', $artisan->max_distance_km) == '5' ? 'selected' : '' }}>5 km</option>
                                        <option value="10" {{ old('max_distance_km', $artisan->max_distance_km) == '10' ? 'selected' : '' }}>10 km</option>
                                        <option value="20" {{ old('max_distance_km', $artisan->max_distance_km) == '20' ? 'selected' : '' }}>20 km</option>
                                        <option value="50" {{ old('max_distance_km', $artisan->max_distance_km) == '50' ? 'selected' : '' }}>50 km</option>
                                        <option value="100" {{ old('max_distance_km', $artisan->max_distance_km) == '100' ? 'selected' : '' }}>Toute la ville</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Adresse précise + Géolocalisation</label>
                                    <input type="text" name="artisan_address" value="{{ old('artisan_address', $artisan->address) }}" placeholder="Ex: 123 Rue de la République, Lomé" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    <button type="button" id="detect-location" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">📍 Utiliser ma position actuelle</button>
                                    <p id="location-status" class="text-xs text-gray-500 mt-1">
                                        @if($artisan->latitude && $artisan->longitude)
                                            Position enregistrée : {{ $artisan->latitude }}, {{ $artisan->longitude }}
                                        @endif
                                    </p>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $artisan->latitude) }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $artisan->longitude) }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Horaire de début</label>
                                        <input type="time" name="availability_start_time" value="{{ old('availability_start_time', $artisan->availability_start_time?->format('H:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Horaire de fin</label>
                                        <input type="time" name="availability_end_time" value="{{ old('availability_end_time', $artisan->availability_end_time?->format('H:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Numéro Mobile Money</label>
                                        <input type="text" name="mobile_money_number" value="{{ old('mobile_money_number', $artisan->mobile_money_number) }}" placeholder="Ex: 90 12 34 56" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Opérateur</label>
                                        <select name="mobile_money_operator" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                            <option value="">-- Choisir --</option>
                                            <option value="moov" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'moov' ? 'selected' : '' }}>Moov Money</option>
                                            <option value="yas" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'yas' ? 'selected' : '' }}>Yas Money</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </details>

                        <!-- 5. LANGUES PARLÉES -->
                        <details class="border rounded-lg p-4 bg-gray-50">
                            <summary class="font-bold text-lg cursor-pointer select-none">🗣️ Langues parlées</summary>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" name="languages[]" value="Français" id="lang_fr" class="rounded" {{ in_array('Français', $artisan->languages ?? []) ? 'checked' : '' }}>
                                    <label for="lang_fr" class="ml-2 text-sm text-gray-700">Français</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="languages[]" value="Éwé" id="lang_ewe" class="rounded" {{ in_array('Éwé', $artisan->languages ?? []) ? 'checked' : '' }}>
                                    <label for="lang_ewe" class="ml-2 text-sm text-gray-700">Éwé</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="languages[]" value="Mina" id="lang_mina" class="rounded" {{ in_array('Mina', $artisan->languages ?? []) ? 'checked' : '' }}>
                                    <label for="lang_mina" class="ml-2 text-sm text-gray-700">Mina</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="languages[]" value="Kabyè" id="lang_kabyè" class="rounded" {{ in_array('Kabyè', $artisan->languages ?? []) ? 'checked' : '' }}>
                                    <label for="lang_kabyè" class="ml-2 text-sm text-gray-700">Kabyè</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="languages[]" value="Anglais" id="lang_en" class="rounded" {{ in_array('Anglais', $artisan->languages ?? []) ? 'checked' : '' }}>
                                    <label for="lang_en" class="ml-2 text-sm text-gray-700">Anglais</label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mt-3">Autres langues</label>
                                    <input type="text" name="other_languages" placeholder="Ex: Arabe, Portugais..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                </div>
                            </div>
                        </details>

                        <!-- BOUTON SUBMIT -->
                        <div class="flex gap-4 pt-6">
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                                💾 Mettre à jour le profil
                            </button>
                            <a href="{{ route('artisan.MaPage') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 font-medium">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</x-app-layout>
