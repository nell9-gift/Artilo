@php
    $artisanUser = auth()->user();
    $artisanInitial = strtoupper(substr($artisanUser->name, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artilo — Espace prestataire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--pro:#7c3aed;--pro-dark:#3b0764;--ink:#25133f;--muted:#6b5b95;--line:#e9ddff;--surface:#fff}*{box-sizing:border-box}body{margin:0;background:#faf8ff;color:var(--ink);font-family:Inter,system-ui,sans-serif}.pro-shell{min-height:100vh;display:grid;grid-template-columns:250px minmax(0,1fr)}.pro-sidebar{background:linear-gradient(165deg,#32105d,#18062f);padding:24px 15px;color:#fff}.pro-brand{font-size:22px;font-weight:800;margin:0 10px 30px}.pro-brand small{display:block;font-size:11px;letter-spacing:1.5px;color:#d8c7ff;text-transform:uppercase;margin-top:4px}.pro-nav a{display:block;color:#ded1f9;text-decoration:none;border-radius:12px;padding:12px 14px;margin:5px 0;font-weight:650}.pro-nav a:hover,.pro-nav a.active{background:rgba(255,255,255,.14);color:#fff}.pro-logout{width:100%;background:transparent;border:0;color:#ded1f9;text-align:left;padding:12px 14px;font:650 14px Inter;cursor:pointer}.pro-main{min-width:0}.pro-header{padding:18px 30px;background:rgba(255,255,255,.92);border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center}.pro-user{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:700}.pro-avatar{width:38px;height:38px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(135deg,var(--pro),#d97706);color:white}.pro-content{max-width:1180px;margin:auto;padding:30px}.pro-card{background:var(--surface);border:1px solid var(--line);border-radius:20px;box-shadow:0 20px 45px -35px #3b0764}.pro-button{display:inline-block;padding:11px 16px;border-radius:11px;background:var(--pro);color:#fff;text-decoration:none;font-size:14px;font-weight:700}.pro-button.secondary{background:#fff;color:var(--pro);border:1px solid var(--line)}.pro-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.pro-stat{padding:20px}.pro-stat b{font-size:30px}.pro-stat span{display:block;color:var(--muted);font-size:13px;margin-top:5px}.pro-mission{display:flex;justify-content:space-between;gap:16px;padding:17px 0;border-bottom:1px solid var(--line)}.pro-mission:last-child{border:0}.pro-mission h3{margin:0 0 5px;font-size:16px}.pro-mission p{margin:0;color:var(--muted);font-size:13px}.pro-badge{display:inline-block;background:#ede9fe;color:#5b21b6;padding:5px 10px;border-radius:99px;font-size:12px;font-weight:700}@media(max-width:760px){.pro-shell{grid-template-columns:1fr}.pro-sidebar{padding:16px}.pro-brand{margin-bottom:12px}.pro-nav{display:flex;overflow:auto;gap:4px}.pro-nav a{white-space:nowrap}.pro-header,.pro-content{padding:18px}.pro-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.pro-mission{flex-direction:column}}
        @yield('styles')
    </style>
</head>
<body>
<div class="pro-shell">
    <aside class="pro-sidebar"><div class="pro-brand">Artilo <small>Espace prestataire</small></div><nav class="pro-nav"><a class="{{ ($pageActive ?? '') === 'dashboard' ? 'active' : '' }}" href="{{ route('artisan.dashboard') }}">Tableau de bord</a><a class="{{ ($pageActive ?? '') === 'missions' ? 'active' : '' }}" href="{{ route('artisan.missions.index') }}">Mes missions</a><a href="{{ route('artisan.profil.edit') }}">Mon profil</a></nav><form method="POST" action="{{ route('logout') }}" style="margin-top:25px">@csrf<button class="pro-logout" type="submit">Déconnexion</button></form></aside>
    <main class="pro-main"><header class="pro-header"><div><strong>{{ $pageTitle ?? 'Espace prestataire' }}</strong><div style="font-size:13px;color:var(--muted);margin-top:3px">{{ $pageSubtitle ?? '' }}</div></div><div class="pro-user"><span class="pro-avatar">{{ $artisanInitial }}</span>{{ $artisanUser->name }}</div></header><div class="pro-content">@if(session('success'))<div class="pro-card" style="padding:14px;margin-bottom:18px;color:#047857">{{ session('success') }}</div>@endif @if(session('info'))<div class="pro-card" style="padding:14px;margin-bottom:18px;color:#1d4ed8">{{ session('info') }}</div>@endif @yield('content')</div></main>
</div>
</body>
</html>
