<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />

        <title>{{ $settings['site_name'] ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            :root {
                /* Couleurs principales */
                --c-primary   : {{ $settings['color_primary']   ?? '#7C3AED' }};
                --c-secondary : {{ $settings['color_secondary'] ?? '#D97706' }};
                --c-text      : {{ $settings['color_text']      ?? '#2E1065' }};
                --c-muted     : {{ $settings['color_muted']     ?? '#6B5B95' }};

                /* Images de fond pour les pages d'auth (url() est indispensable) */
                --auth-bg-1   : url('{{ asset($settings['auth_background_image_1'] ?? 'images/artisan_btp.png') }}');
                --auth-bg-2   : url('{{ asset($settings['auth_background_image_2'] ?? 'images/artisan_charpenterie.png') }}');
                --auth-bg-3   : url('{{ asset($settings['auth_background_image_3'] ?? 'images/artisan_electricien.png') }}');
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/auth-register-login.css', 'resources/js/app.js']) 
        @stack('styles')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{ $slot }}

        <script>
            window.addEventListener('pageshow', function (event) {
                var navigationEntries = performance.getEntriesByType && performance.getEntriesByType('navigation');
                var isBackForward = event.persisted || (navigationEntries && navigationEntries[0] && navigationEntries[0].type === 'back_forward');

                if (isBackForward) {
                    window.location.href = window.location.href;
                }
            });

            window.addEventListener('unload', function () {
                // Ajoute un gestionnaire unload pour réduire le risque de cache de page navigateur
            });
        </script>
    </body>
</html>