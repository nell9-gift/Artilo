<x-guest-layout>

    @push('styles')
        @vite(['resources/css/auth-register-unified.css'])
    @endpush

    <div class="auth-wrapper">

        <!-- Arrière-plan animé (3 photos en alternance, via $settings) -->
        <div class="auth-bg">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="auth-shell">

            <!-- ═══════ COLONNE GAUCHE (fixe, ne disparaît jamais) ═══════ -->
            <div class="auth-left">
                <div class="auth-left-content">
                    <span class="brand-name">{{ $settings['site_name'] ?? 'Artilo' }}</span>
                    <h2>Rejoignez Artilo</h2>
                    <p>Choisissez votre profil pour créer votre compte</p>

                    <div class="tab-switch">
                        <button type="button" class="tab-btn {{ ($activeTab ?? 'customer') === 'customer' ? 'active' : '' }}" data-tab="customer" id="btn-customer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Particulier
                        </button>
                        <button type="button" class="tab-btn {{ ($activeTab ?? 'customer') === 'artisan' ? 'active' : '' }}" data-tab="artisan" id="btn-artisan">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.3-3.3a5 5 0 01-6.7 6.7L4.6 22 2 19.4l10.3-10.3a5 5 0 016.7-6.7l-3.3 3.3z" />
                            </svg>
                            Artisan
                        </button>
                    </div>

                    <p class="left-note">
                        Déjà inscrit ? <a href="{{ route('login') }}">Connectez-vous ici</a>
                    </p>
                </div>
            </div>

            <!-- ═══════ COLONNE DROITE (formulaire qui change) ═══════ -->
            <div class="auth-right">

                <!-- ── Formulaire PARTICULIER ── -->
                <div class="form-pane {{ ($activeTab ?? 'customer') === 'customer' ? 'active' : '' }}" data-pane="customer">

                    <h1>Créer un compte particulier</h1>
                    <p class="subtitle">Inscrivez-vous pour accéder à votre espace</p>

                    <form method="POST" action="{{ route('register.customer.store') }}">
                        @csrf

                        <div class="field-group">
                            <label for="name">Nom complet</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Entrez votre nom complet" required>
                            </div>
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="email">Email</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Entrez votre email" required>
                            </div>
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="telephone">Téléphone</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.27 1.06L8 10.5a11 11 0 005.5 5.5l1.26-1.46a1 1 0 011.06-.27l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.4 21 3 14.6 3 6V5z" />
                                </svg>
                                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Entrez votre numéro" required>
                            </div>
                            @error('telephone')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

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
                                @error('password')<span class="field-error">{{ $message }}</span>@enderror
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

                        <button type="submit" class="btn-submit">
                            S'inscrire
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7M21 12H3" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- ── Formulaire ARTISAN ── -->
                <div class="form-pane {{ ($activeTab ?? 'customer') === 'artisan' ? 'active' : '' }}" data-pane="artisan">

                    <span class="badge">Espace professionnel</span>
                    <h1>Devenez artisan partenaire</h1>
                    <p class="subtitle">Inscrivez-vous pour proposer vos services aux particuliers</p>

                    <form method="POST" action="{{ route('register.artisan.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="field-group">
                            <label for="a_name">Nom complet</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <input type="text" id="a_name" name="name" value="{{ old('name') }}" placeholder="Entrez votre nom complet" required>
                            </div>
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="a_email">Email</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <input type="email" id="a_email" name="email" value="{{ old('email') }}" placeholder="Entrez votre email" required>
                            </div>
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="a_telephone">Téléphone</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.27 1.06L8 10.5a11 11 0 005.5 5.5l1.26-1.46a1 1 0 011.06-.27l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.4 21 3 14.6 3 6V5z" />
                                </svg>
                                <input type="tel" id="a_telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Entrez votre numéro" required>
                            </div>
                            @error('telephone')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="profession">Métier</label>
                            <div class="input-wrap select-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7L13.5 14.5L8.5 9.5L3 15M21 7H15M21 7V13" />
                                </svg>
                                <select id="profession" name="profession" required>
                                    <option value="" disabled {{ old('profession') ? '' : 'selected' }}>Sélectionnez votre métier</option>
                                    <option value="menuisier" {{ old('profession') == 'menuisier' ? 'selected' : '' }}>Menuisier</option>
                                    <option value="macon" {{ old('profession') == 'macon' ? 'selected' : '' }}>Maçon</option>
                                    <option value="electricien" {{ old('profession') == 'electricien' ? 'selected' : '' }}>Électricien</option>
                                    <option value="plombier" {{ old('profession') == 'plombier' ? 'selected' : '' }}>Plombier</option>
                                    <option value="peintre" {{ old('profession') == 'peintre' ? 'selected' : '' }}>Peintre en bâtiment</option>
                                    <option value="carreleur" {{ old('profession') == 'carreleur' ? 'selected' : '' }}>Carreleur</option>
                                    <option value="couvreur" {{ old('profession') == 'couvreur' ? 'selected' : '' }}>Couvreur</option>
                                    <option value="serrurier" {{ old('profession') == 'serrurier' ? 'selected' : '' }}>Serrurier</option>
                                    <option value="autre" {{ old('profession') == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                            @error('profession')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field-group">
                            <label for="intervention_area">Zone d'intervention</label>
                            <div class="input-wrap">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                    <circle cx="12" cy="11" r="2.5" />
                                </svg>
                                <input type="text" id="intervention_area" name="intervention_area" value="{{ old('intervention_area') }}" placeholder="Ville, région ou rayon d'action" required>
                            </div>
                            @error('intervention_area')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="password-row">
                            <div class="field-group">
                                <label for="a_password">Mot de passe</label>
                                <div class="input-wrap">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                                    </svg>
                                    <input type="password" id="a_password" name="password" class="has-toggle" placeholder="••••••••" required>
                                    <svg class="toggle-password" data-target="a_password" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </div>
                                @error('password')<span class="field-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field-group">
                                <label for="a_password_confirmation">Confirmer</label>
                                <div class="input-wrap">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                                    </svg>
                                    <input type="password" id="a_password_confirmation" name="password_confirmation" class="has-toggle" placeholder="••••••••" required>
                                    <svg class="toggle-password" data-target="a_password_confirmation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </div>
                            </div>
                        </div>

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
                            @error('identity_document')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="btn-submit">
                            Envoyer ma candidature
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7M21 12H3" />
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Bascule entre les 2 formulaires sans recharger la page
        const tabButtons = document.querySelectorAll('.tab-btn');
        const panes = document.querySelectorAll('.form-pane');
        const urls = {
            customer: '{{ route('register') }}',
            artisan: '{{ route('register.artisan') }}',
        };

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                tabButtons.forEach(b => b.classList.toggle('active', b === btn));
                panes.forEach(p => p.classList.toggle('active', p.dataset.pane === tab));
                window.history.replaceState({}, '', urls[tab]);
            });
        });

        // Affiche/masque le mot de passe
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', () => {
                const target = document.getElementById(icon.dataset.target);
                if (!target) return;
                target.type = target.type === 'password' ? 'text' : 'password';
            });
        });

        // Zone d'upload : affiche le nom du fichier choisi
        document.querySelectorAll('.upload-zone').forEach(zone => {
            const input = zone.querySelector('input[type="file"]');
            const fileNameEl = zone.querySelector('.file-name');
            if (!input) return;

            input.addEventListener('change', () => {
                fileNameEl.textContent = input.files.length ? input.files[0].name : '';
            });

            ['dragover', 'dragleave', 'drop'].forEach(evt => {
                zone.addEventListener(evt, e => {
                    e.preventDefault();
                    zone.classList.toggle('is-dragover', evt === 'dragover');
                });
            });
        });
    </script>

</x-guest-layout>