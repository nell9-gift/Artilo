<x-guest-layout>

    {{-- Styles et scripts dédiés à la page artisan --}}
   

    <div class="auth-wrapper">

        <!-- Arrière-plan animé : atelier, chantier, menuiserie -->
        <div class="auth-bg">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Carte du formulaire d'inscription -->
        <div class="auth-card">

            <span class="badge">Espace professionnel</span>
            <h1>Devenez artisan partenaire</h1>
            <p class="subtitle">Inscrivez-vous pour proposer vos services aux particuliers</p>

            <!-- Début du formulaire -->
            <!-- enctype obligatoire pour permettre l'upload du document d'identité -->
            <form method="POST" action="{{ route('register.artisan.store') }}" enctype="multipart/form-data">

                @csrf

                <!-- Nom complet -->
                <div class="field-group">
                    <label for="name">Nom complet</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Entrez votre nom complet" required>
                    </div>
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="field-group">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Entrez votre email" required>
                    </div>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div class="field-group">
                    <label for="telephone">Téléphone</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.27 1.06L8 10.5a11 11 0 005.5 5.5l1.26-1.46a1 1 0 011.06-.27l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.4 21 3 14.6 3 6V5z" />
                        </svg>
                        <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Entrez votre numéro" required>
                    </div>
                    @error('telephone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Profession (métier du BTP / artisanat) -->
                <div class="field-group">
                    <label for="profession">Métier</label>
                    <div class="input-wrap select-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7L13.5 14.5L8.5 9.5L3 15M21 7H15M21 7V13" />
                        </svg>
                        <select id="profession" name="profession" required style="color-scheme: light;">
                            <option value="" disabled style="color:#1a1410;background:#fff;" {{ old('profession') ? '' : 'selected' }}>Sélectionnez votre métier</option>
                            <option value="menuisier" style="color:#1a1410;background:#fff;" {{ old('profession') == 'menuisier' ? 'selected' : '' }}>Menuisier</option>
                            <option value="macon" style="color:#1a1410;background:#fff;" {{ old('profession') == 'macon' ? 'selected' : '' }}>Maçon</option>
                            <option value="electricien" style="color:#1a1410;background:#fff;" {{ old('profession') == 'electricien' ? 'selected' : '' }}>Électricien</option>
                            <option value="plombier" style="color:#1a1410;background:#fff;" {{ old('profession') == 'plombier' ? 'selected' : '' }}>Plombier</option>
                            <option value="peintre" style="color:#1a1410;background:#fff;" {{ old('profession') == 'peintre' ? 'selected' : '' }}>Peintre en bâtiment</option>
                            <option value="carreleur" style="color:#1a1410;background:#fff;" {{ old('profession') == 'carreleur' ? 'selected' : '' }}>Carreleur</option>
                            <option value="couvreur" style="color:#1a1410;background:#fff;" {{ old('profession') == 'couvreur' ? 'selected' : '' }}>Couvreur</option>
                            <option value="serrurier" style="color:#1a1410;background:#fff;" {{ old('profession') == 'serrurier' ? 'selected' : '' }}>Serrurier</option>
                            <option value="autre" style="color:#1a1410;background:#fff;" {{ old('profession') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    @error('profession')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Zone d'intervention -->
                <div class="field-group">
                    <label for="intervention_area">Zone d'intervention</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <circle cx="12" cy="11" r="2.5" />
                        </svg>
                        <input type="text" id="intervention_area" name="intervention_area" value="{{ old('intervention_area') }}" placeholder="Ville, région ou rayon d'action" required>
                    </div>
                    @error('intervention_area')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mot de passe + confirmation -->
                <div class="password-row">

                    <div class="field-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrap">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                            </svg>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="••••••••" required>
                            <svg class="toggle-password" data-target="password" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password_confirmation">Confirmer</label>
                        <div class="input-wrap">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                            </svg>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="••••••••" required>
                            <svg class="toggle-password" data-target="password_confirmation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- Document d'identité (zone de glisser-déposer) -->
                <div class="field-group">
                    <label for="identity_document">Pièce d'identité (carte / passeport)</label>
                    <div class="upload-zone">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />
                        </svg>
                        <p class="upload-text"><strong>Cliquez pour téléverser</strong> ou glissez-déposez<br>JPG, PNG ou PDF — 5 Mo max</p>
                        <p class="file-name"></p>
                        <input type="file" id="identity_document" name="identity_document" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    @error('identity_document')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Bouton d'envoi -->
                <button type="submit" class="btn-submit">
                    Envoyer ma candidature
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7M21 12H3" />
                    </svg>
                </button>

            </form>

            <p class="login-link">
                Vous avez déjà un compte ? <a href="{{ route('login') }}">Connectez-vous ici</a>
            </p>

        </div>

    </div>

</x-guest-layout>