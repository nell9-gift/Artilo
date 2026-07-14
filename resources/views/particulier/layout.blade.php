{{-- ========================================================================
   ARTILO — Layout glassmorphism pour le module Particulier
   Sidebar violette · Topbar vitré · Fond dégradé · Animations GSAP-like
   ======================================================================== --}}
@php
use App\Models\Setting;
$settings = Setting::pluck('value', 'key')->toArray();
$C = [
    'primary'    => $settings['color_primary']         ?? '#7C3AED',
    'p_dark'     => $settings['color_primary_dark']    ?? '#6D28D9',
    'p_900'      => $settings['color_primary_900']     ?? '#2E1065',
    'p_800'      => $settings['color_primary_800']     ?? '#4C1D95',
    'p_light'    => $settings['color_primary_light']   ?? '#A78BFA',
    'secondary'  => $settings['color_secondary']       ?? '#D97706',
    'sec_light'  => $settings['color_secondary_light'] ?? '#F59E0B',
    'bg'         => $settings['color_bg']              ?? '#FFFFFF',
    'bg2'        => $settings['color_bg_2']            ?? '#FAF8FF',
    'text'       => $settings['color_text']            ?? '#2E1065',
    'muted'      => $settings['color_muted']           ?? '#6B5B95',
];
$SITE = $settings['site_name'] ?? 'Artilo';
$logo = $settings['logo'] ?? 'images/logo.jpeg';
$user = Auth::user();
$initial = strtoupper(substr($user->name, 0, 1));

function icon_p(string $n): string {
    $p = [
        'grid'    => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
        'plus'    => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'tasks'   => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'arrow'   => '<line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>',
        'logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'menu'    => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
        'bell'    => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'chat'    => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">'.($p[$n] ?? '').'</svg>';
}
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>{{ $SITE }} — Espace client</title>
    <link rel="icon" href="{{ asset($settings['favicon'] ?? null) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .al-dash{
          --primary:<?= e($C['primary']) ?>;
          --p-dark:<?= e($C['p_dark']) ?>;
          --p-900:<?= e($C['p_900']) ?>;
          --p-800:<?= e($C['p_800']) ?>;
          --p-light:<?= e($C['p_light']) ?>;
          --secondary:<?= e($C['secondary']) ?>;
          --sec-light:<?= e($C['sec_light']) ?>;
          --bg:<?= e($C['bg']) ?>;
          --bg2:<?= e($C['bg2']) ?>;
          --text:<?= e($C['text']) ?>;
          --muted:<?= e($C['muted']) ?>;
          --glass: rgba(255,255,255,.55);
          --glass-strong: rgba(255,255,255,.72);
          --glass-brd: rgba(255,255,255,.65);
          --shadow: 0 20px 50px -20px rgba(46,16,101,.45);
          --shadow-sm: 0 8px 24px -14px rgba(46,16,101,.5);
          --radius: 22px;
          --sidebar: 268px;
          position:relative;
          font-family:'Inter',system-ui,sans-serif;
          color:var(--text);
          background:
            radial-gradient(1200px 700px at 82% -10%, rgba(124,58,237,.22), transparent 60%),
            radial-gradient(900px 600px at -5% 15%, rgba(217,119,6,.14), transparent 55%),
            radial-gradient(1000px 800px at 60% 110%, rgba(167,139,250,.20), transparent 60%),
            linear-gradient(160deg, var(--bg2) 0%, #F3EEFF 45%, #FBF7FF 100%);
          -webkit-font-smoothing:antialiased;
        }
        .al-dash *{box-sizing:border-box}
        .al-dash h1,.al-dash h2,.al-dash h3,.al-dash h4{font-family:'Sora',sans-serif;letter-spacing:-.02em;margin:0}
        .al-dash a{color:inherit;text-decoration:none}
        .al-dash svg{width:20px;height:20px;display:block}
        .al-dash ::selection{background:var(--p-light);color:#fff}

        .al-dash .orb{position:absolute;border-radius:50%;filter:blur(60px);opacity:.5;z-index:0;pointer-events:none;animation:al-float 16s ease-in-out infinite}
        .al-dash .orb.o1{width:420px;height:420px;background:radial-gradient(circle,rgba(124,58,237,.55),transparent 70%);top:-140px;right:-80px}
        .al-dash .orb.o2{width:360px;height:360px;background:radial-gradient(circle,rgba(245,158,11,.4),transparent 70%);bottom:-120px;left:12%;animation-delay:-6s}
        @keyframes al-float{0%,100%{transform:translateY(0) translateX(0)}50%{transform:translateY(-34px) translateX(20px)}}

        .al-dash .shell{position:relative;z-index:1;display:flex;min-height:100vh}

        .al-dash .sidebar{
          width:var(--sidebar);flex-shrink:0;position:sticky;top:0;height:100vh;
          display:flex;flex-direction:column;gap:6px;padding:22px 16px;
          background:linear-gradient(185deg, rgba(46,16,101,.92), rgba(76,29,149,.88));
          backdrop-filter:blur(18px);
          border-right:1px solid rgba(255,255,255,.08);
          color:#EDE7FF;
        }
        .al-dash .brand{display:flex;align-items:center;gap:12px;padding:6px 10px 18px}
        .al-dash .brand .logo{width:44px;height:44px;border-radius:13px;object-fit:cover;
          box-shadow:0 8px 20px -6px rgba(0,0,0,.5);border:1.5px solid rgba(255,255,255,.25);background:#fff}
        .al-dash .brand b{font-family:'Sora';font-size:20px;color:#fff;letter-spacing:-.03em}
        .al-dash .brand span{display:block;font-size:11px;color:var(--p-light);font-weight:500;letter-spacing:.14em;text-transform:uppercase}
        .al-dash .nav{display:flex;flex-direction:column;gap:3px;overflow-y:auto;flex:1;padding-right:4px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.25) transparent}
        .al-dash .nav::-webkit-scrollbar{width:6px}
        .al-dash .nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.18);border-radius:10px}
        .al-dash .nav-item{
          display:flex;align-items:center;gap:13px;padding:11px 13px;border-radius:13px;
          color:#CFC3F5;font-size:14.5px;font-weight:500;cursor:pointer;position:relative;
          transition:.22s cubic-bezier(.4,0,.2,1);border:1px solid transparent;
        }
        .al-dash .nav-item svg{width:19px;height:19px;opacity:.85;transition:.22s}
        .al-dash .nav-item:hover{background:rgba(255,255,255,.08);color:#fff;transform:translateX(3px)}
        .al-dash .nav-item.active{
          background:linear-gradient(100deg, rgba(167,139,250,.28), rgba(124,58,237,.12));
          color:#fff;border-color:rgba(167,139,250,.35);
          box-shadow:inset 0 0 0 1px rgba(255,255,255,.05), 0 8px 20px -12px rgba(0,0,0,.6);
        }
        .al-dash .nav-item.active::before{content:"";position:absolute;left:-16px;top:50%;transform:translateY(-50%);
          width:4px;height:22px;border-radius:4px;background:linear-gradient(var(--sec-light),var(--secondary));box-shadow:0 0 12px var(--secondary)}
        .al-dash .nav-item.active svg{opacity:1}
        .al-dash .nav-sep{height:1px;background:rgba(255,255,255,.09);margin:10px 6px}
        .al-dash .logout{
          display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:13px;cursor:pointer;
          color:#FBD9C6;font-weight:600;font-size:14.5px;
          background:rgba(217,119,6,.14);border:1px solid rgba(245,158,11,.28);transition:.22s;
        }
        .al-dash .logout:hover{background:rgba(217,119,6,.28);color:#fff;transform:translateY(-1px)}

        .al-dash .main{flex:1;min-width:0;display:flex;flex-direction:column}
        .al-dash .topbar{
          position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:16px;
          padding:16px 26px;background:var(--glass);backdrop-filter:blur(16px);
          border-bottom:1px solid var(--glass-brd);
        }
        .al-dash .burger{display:none;background:var(--glass-strong);border:1px solid var(--glass-brd);border-radius:12px;
          width:42px;height:42px;place-items:center;color:var(--p-800);cursor:pointer}
        .al-dash .page-title h1{font-size:22px;line-height:1.1}
        .al-dash .page-title p{font-size:13px;color:var(--muted);margin-top:2px}
        .al-dash .icon-btn{position:relative;width:44px;height:44px;flex-shrink:0;display:grid;place-items:center;
          background:var(--glass-strong);border:1px solid var(--glass-brd);border-radius:14px;color:var(--p-800);cursor:pointer;transition:.2s}
        .al-dash .icon-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm);color:var(--primary)}
        .al-dash .avatar-btn{display:flex;align-items:center;gap:10px;background:var(--glass-strong);border:1px solid var(--glass-brd);
          border-radius:14px;padding:6px 12px 6px 6px;cursor:pointer;transition:.2s}
        .al-dash .avatar-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm)}
        .al-dash .avatar-circle{width:34px;height:34px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
          background:linear-gradient(135deg,var(--primary),var(--p-dark));color:#fff;font-family:'Sora';font-weight:700;font-size:14px}
        .al-dash .avatar-btn .nm{font-size:13.5px;font-weight:600;line-height:1.1}
        .al-dash .avatar-btn .rl{font-size:11px;color:var(--muted)}

        .al-dash .content{padding:26px;max-width:1320px;width:100%;margin:0 auto}

        .al-dash .card{
          background:var(--glass);backdrop-filter:blur(16px);
          border:1px solid var(--glass-brd);border-radius:var(--radius);
          box-shadow:var(--shadow);
        }
        .al-dash .card.solid{background:var(--glass-strong)}
        .al-dash .reveal{opacity:0;transform:translateY(22px)}
        .al-dash .reveal.in{opacity:1;transform:none;transition:.7s cubic-bezier(.2,.8,.2,1)}

        .al-dash .sec-head{display:flex;align-items:center;justify-content:space-between;margin:0 0 16px}
        .al-dash .sec-head h2{font-size:18px}
        .al-dash .sec-head .link{font-size:13px;color:var(--primary);font-weight:600;display:flex;align-items:center;gap:5px;cursor:pointer}
        .al-dash .sec-head .link svg{width:15px;height:15px}

        .al-dash .muted{color:var(--muted)}
        .al-dash .mb-18{margin-bottom:18px}

        .al-dash .btn{display:inline-flex;align-items:center;gap:8px;font-family:'Sora';font-weight:600;font-size:14px;
          padding:12px 20px;border-radius:14px;border:none;cursor:pointer;transition:.22s}
        .al-dash .btn-primary{background:linear-gradient(120deg,var(--primary),var(--p-dark));color:#fff;
          box-shadow:0 14px 28px -12px rgba(124,58,237,.8)}
        .al-dash .btn-primary:hover{transform:translateY(-2px);box-shadow:0 20px 34px -12px rgba(124,58,237,.9)}
        .al-dash .btn-ghost{background:var(--glass-strong);border:1px solid var(--glass-brd);color:var(--p-800)}
        .al-dash .btn-ghost:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm)}
        .al-dash .btn svg{width:18px;height:18px}

        .al-dash .badge{font-size:11.5px;font-weight:700;padding:4px 11px;border-radius:20px;white-space:nowrap}
        .al-dash .st-blue{background:rgba(59,130,246,.15);color:#1d4ed8}
        .al-dash .st-green{background:rgba(16,185,129,.15);color:#047857}
        .al-dash .st-amber{background:rgba(217,119,6,.16);color:#b45309}
        .al-dash .st-red{background:rgba(239,68,68,.14);color:#b91c1c}
        .al-dash .st-gray{background:rgba(107,91,149,.15);color:var(--muted)}

        .al-dash .overlay{position:fixed;inset:0;background:rgba(46,16,101,.45);backdrop-filter:blur(3px);z-index:40;opacity:0;visibility:hidden;transition:.3s}
        .al-dash .overlay.show{opacity:1;visibility:visible}

        @media(max-width:900px){
          .al-dash .sidebar{position:fixed;left:0;top:0;z-index:50;transform:translateX(-105%);transition:.32s cubic-bezier(.4,0,.2,1);box-shadow:0 0 60px rgba(0,0,0,.4)}
          .al-dash .sidebar.open{transform:none}
          .al-dash .burger{display:grid}
        }

        @yield('styles')
    </style>
</head>
<body>
<div class="al-dash">
    <div class="orb o1"></div>
    <div class="orb o2"></div>

    <div class="shell">
        {{-- SIDEBAR --}}
        <aside class="sidebar" id="al-sidebar">
            <div class="brand">
                <img class="logo" src="{{ asset($logo) }}" alt="{{ $SITE }}"
                     onerror="this.style.background='linear-gradient(135deg,#7C3AED,#F59E0B)';this.removeAttribute('src')">
                <div>
                    <b>{{ $SITE }}</b>
                    <span>Espace client</span>
                </div>
            </div>
            <nav class="nav">
                <a href="{{ route('particulier.dashboard') }}" class="nav-item {{ $pageActive === 'dashboard' ? 'active' : '' }}">
                    <?= icon_p('grid') ?><span>Tableau de bord</span>
                </a>
                <a href="{{ route('particulier.demande.create') }}" class="nav-item {{ $pageActive === 'nouvelle' ? 'active' : '' }}">
                    <?= icon_p('plus') ?><span>Nouvelle demande</span>
                </a>
                <a href="{{ route('particulier.dashboard') }}#missions" class="nav-item {{ $pageActive === 'missions' ? 'active' : '' }}">
                    <?= icon_p('tasks') ?><span>Mes missions</span>
                </a>
            </nav>
            <div class="nav-sep"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout" style="width:100%;text-align:left;border-style:solid" onclick="return confirm('Se déconnecter ?')">
                    <?= icon_p('logout') ?><span>Déconnexion</span>
                </button>
            </form>
        </aside>
        <div class="overlay" id="al-overlay"></div>

        {{-- MAIN --}}
        <div class="main">
            <header class="topbar">
                <button class="burger" id="al-burger" aria-label="Menu"><?= icon_p('menu') ?></button>
                <div class="page-title">
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $pageSubtitle }}</p>
                </div>
                <button class="icon-btn" aria-label="Notifications"><?= icon_p('bell') ?><span class="dot"></span></button>
                <button class="avatar-btn">
                    <span class="avatar-circle">{{ $initial }}</span>
                    <span class="who">
                        <span class="nm">{{ $user->name }}</span>
                        <span class="rl">{{ $user->role === 'customer' ? 'Particulier' : 'Client' }}</span>
                    </span>
                </button>
            </header>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const sidebar = document.getElementById('al-sidebar');
    const overlay = document.getElementById('al-overlay');
    const burger  = document.getElementById('al-burger');
    if(burger) burger.addEventListener('click', function(){sidebar.classList.add('open');overlay.classList.add('show');});
    if(overlay) overlay.addEventListener('click', function(){sidebar.classList.remove('open');overlay.classList.remove('show');});

    document.querySelectorAll('.reveal').forEach((el,i)=>{
        setTimeout(()=>el.classList.add('in'), i*70);
    });
    // Animer les barres de progression
    document.querySelectorAll('.progress i').forEach(p=>p.style.width=p.dataset.w+'%');
    // Animer les compteurs
    document.querySelectorAll('.k-val[data-count]').forEach(function(el){
        if(el.dataset.done) return; el.dataset.done=1;
        const raw = el.dataset.count; const num = parseInt(raw,10);
        if(isNaN(num)) return;
        const dur=1000, t0=performance.now();
        function step(t){
            const p=Math.min((t-t0)/dur,1);
            const val=Math.floor(num*(1-Math.pow(1-p,3)));
            el.textContent = val.toLocaleString('fr-FR');
            if(p<1) requestAnimationFrame(step); else el.textContent = num.toLocaleString('fr-FR');
        }
        requestAnimationFrame(step);
    });
    @yield('scripts')
});
</script>
@stack('scripts')
</body>
</html>
