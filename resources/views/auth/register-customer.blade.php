<x-guest-layout>
 
    {{-- Les styles et scripts sont chargés via Vite --}}
    <div class="auth-wrapper">
 
        <!-- Arrière-plan animé -->
        <div class="auth-bg">
            <span></span>
            <span></span>
            <span></span>
        </div>
 
        <!-- Carte du formulaire d'inscription -->
        <div class="auth-card">
 
            <h1>Créer un compte en tant que particulier</h1>
            <p class="subtitle">Inscrivez-vous pour accéder à votre espace</p>
 
            <!-- Début du formulaire -->
            <!-- method="POST" : envoie les données au serveur -->
            <!-- action : envoie les données vers la route register.customer.store -->
            <form method="POST" action="{{ route('register.customer.store') }}">
 
                <!-- Protection contre les attaques CSRF -->
                @csrf
 
                <!-- Champ Nom complet -->
                <div class="field-group">
                    <label for="name">Nom complet</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <!-- old('name') permet de conserver la valeur saisie en cas d'erreur -->
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Entrez votre nom complet" required>
                    </div>
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
 
                <!-- Champ Email -->
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
 
                <!-- Champ Téléphone -->
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
 
                <!-- Mot de passe + confirmation côte à côte -->
                <div class="password-row">
 
                    <!-- Champ Mot de passe -->
                    <div class="field-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-wrap">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                            </svg>
                            <!-- Le mot de passe saisi n'est pas affiché à l'écran -->
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="••••••••" required>
 
                            <!-- Icône œil pour afficher/masquer (gérée par auth-register.js) -->
                            <svg class="toggle-password" data-target="password" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                    </div>
 
                    <!-- Champ Confirmation du mot de passe -->
                    <div class="field-group">
                        <label for="password_confirmation">Confirmer</label>
                        <div class="input-wrap">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                            </svg>
                            <!-- Ce champ sert à vérifier que les deux mots de passe sont identiques -->
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="••••••••" required>
 
                            <svg class="toggle-password" data-target="password_confirmation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                    </div>
 
                </div>
 
                <!-- Bouton qui envoie le formulaire -->
                <button type="submit" class="btn-submit">
                    S'inscrire
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
