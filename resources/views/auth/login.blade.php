<x-guest-layout>

    <div class="auth-wrapper">

        <!-- Arrière-plan animé -->
        <div class="auth-bg">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Carte du formulaire de connexion -->
        <div class="auth-card">

            <h1>{{ $settings['auth_title'] ?? 'Bon retour' }}</h1>
            <p class="subtitle">{{ $settings['auth_subtitle'] ?? 'Connectez-vous pour accéder à votre espace' }}</p>

            <!-- Statut de session (ex: mot de passe réinitialisé) -->
           @if (session('status'))
    <div id="toast" class="toast">
        {{ session('status') }}
    </div>
@endif
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="field-group">
                    <label for="email">{{ $settings['auth_email_label'] ?? 'Email' }}</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ $settings['auth_email_placeholder'] ?? 'Entrez votre email' }}" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="field-group">
                    <label for="password">{{ $settings['auth_password_label'] ?? 'Mot de passe' }}</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v2" />
                        </svg>
                        <input type="password" id="password" name="password" class="has-toggle" placeholder="{{ $settings['auth_password_placeholder'] ?? '••••••••' }}" required autocomplete="current-password">
                        <svg class="toggle-password" data-target="password" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Se souvenir de moi + mot de passe oublié -->
                <div class="login-options">
                    <label class="remember-me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ $settings['auth_remember_text'] ?? 'Se souvenir de moi' }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            {{ $settings['auth_forgot_text'] ?? 'Mot de passe oublié ?' }}
                        </a>
                    @endif
                </div>

                <!-- Bouton de connexion -->
                <button type="submit" class="btn-submit">
                    {{ $settings['auth_login_button'] ?? 'Se connecter' }}
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7M21 12H3" />
                    </svg>
                </button>

                <!-- Séparateur -->
                <div class="divider">
                    <span>{{ $settings['auth_or_text'] ?? 'ou' }}</span>
                </div>

                <!-- Connexion via Google -->
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62z"/>
                        <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.55-1.84.86-3.06.86-2.36 0-4.36-1.6-5.07-3.74H.93v2.33A9 9 0 0 0 9 18z"/>
                        <path fill="#FBBC05" d="M3.93 10.68A5.4 5.4 0 0 1 3.64 9c0-.58.1-1.16.29-1.68V4.99H.93A9 9 0 0 0 0 9c0 1.45.35 2.83.93 4.01l3-2.33z"/>
                        <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58A9 9 0 0 0 9 0 9 9 0 0 0 .93 4.99l3 2.33C4.64 5.18 6.64 3.58 9 3.58z"/>
                    </svg>
                    {{ $settings['auth_google_button'] ?? 'Continuer avec Google' }}
                </a>

            </form>

            <p class="login-link">
                {{ $settings['auth_register_question'] ?? 'Pas encore de compte ?' }}
                <a href="{{ route('register') }}">{{ $settings['auth_register_link_text'] ?? 'Inscrivez-vous ici' }}</a>
            </p>

        </div>

    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', () => {
                const target = document.getElementById(icon.dataset.target);
                if (!target) return;
                target.type = target.type === 'password' ? 'text' : 'password';
            });
        });
        const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => toast.classList.add('is-visible'), 50);
        setTimeout(() => {
            toast.classList.remove('is-visible');
            setTimeout(() => toast.remove(), 400);
        }, 5000);
    }

    </script>

</x-guest-layout>