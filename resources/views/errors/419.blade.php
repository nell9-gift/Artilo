@php
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $color_primary = $settings['color_primary'] ?? '#7C3AED';
    $color_primary_dark = $settings['color_primary_dark'] ?? '#6D28D9';
    $color_secondary = $settings['color_secondary'] ?? '#D97706';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Session expirée - Artilo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, {{ $color_primary }}, {{ $color_primary_dark }});
            padding: 1.5rem;
        }
        .box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(18px) saturate(140%);
            -webkit-backdrop-filter: blur(18px) saturate(140%);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 460px;
            text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,0.25);
            color: #fff;
        }
        .icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        p {
            color: rgba(255,255,255,0.85);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, {{ $color_secondary }}, {{ $color_secondary }});
            color: #fff;
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">⏳</div>
        <h1>Votre session a expiré</h1>
        <p>
            Pour des raisons de sécurité, votre session s'est terminée après une longue période d'inactivité.
            Merci de recharger la page et de réessayer.
        </p>
        <a href="{{ url()->previous() }}" class="btn">Réessayer</a>
    </div>
</body>
</html>