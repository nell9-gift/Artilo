@extends('particulier.layout', [
    'pageActive' => 'dashboard',
    'pageTitle' => 'Tableau de bord',
    'pageSubtitle' => 'Suivez vos demandes et vos interventions.',
])

@section('styles')
    .customer-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}.customer-stat{padding:20px}.customer-stat b{font:800 30px/1 'Sora';display:block;color:var(--primary)}.customer-stat span{display:block;margin-top:8px;color:var(--muted);font-size:13px;font-weight:600}.mission-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 0;border-bottom:1px solid rgba(124,58,237,.1)}.mission-row:last-child{border:0}.mission-row h3{font-size:15px;margin:0 0 4px}.mission-row p{margin:0;color:var(--muted);font-size:13px}.status{padding:5px 10px;border-radius:99px;font-size:12px;font-weight:700}.status-warning{background:#fef3c7;color:#92400e}.status-info{background:#dbeafe;color:#1d4ed8}.status-success{background:#d1fae5;color:#047857}.status-danger{background:#fee2e2;color:#b91c1c}@media(max-width:800px){.customer-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.mission-row{align-items:flex-start;flex-direction:column}}
@endsection

@section('content')
    <div class="flex items-center justify-between gap-4 mb-5 reveal">
        <div><h2 style="font-size:20px">Bonjour {{ auth()->user()->name }}</h2><p class="muted" style="margin:4px 0 0">Voici l'état de vos demandes.</p></div>
        <a class="btn btn-primary" href="{{ route('particulier.demande.create') }}">{!! icon_p('plus') !!} Nouvelle demande</a>
    </div>
    <div class="customer-stats reveal">
        <div class="card customer-stat"><b>{{ $stats['total'] }}</b><span>Demandes envoyées</span></div>
        <div class="card customer-stat"><b>{{ $stats['en_attente'] }}</b><span>En recherche</span></div>
        <div class="card customer-stat"><b>{{ $stats['en_cours'] }}</b><span>Interventions actives</span></div>
        <div class="card customer-stat"><b>{{ $stats['terminees'] }}</b><span>Terminées</span></div>
    </div>
    <section class="card reveal" id="missions" style="padding:22px">
        <div class="sec-head"><h2>Mes missions</h2><a class="link" href="{{ route('particulier.demande.create') }}">Créer une demande {!! icon_p('arrow') !!}</a></div>
        @forelse ($missions as $mission)
            @php($tone = match($mission->statut_couleur) {'warning' => 'warning', 'success' => 'success', 'danger' => 'danger', default => 'info'})
            <article class="mission-row">
                <div><h3>{{ ucfirst($mission->metier_requis) }}</h3><p>{{ $mission->adresse }} · {{ $mission->created_at->format('d/m/Y') }}@if($mission->artisan) · {{ $mission->artisan->user?->name }}@endif</p></div>
                <div class="flex items-center gap-3"><span class="status status-{{ $tone }}">{{ $mission->statut_libelle }}</span><a class="link" href="{{ route('particulier.mission.suivi', $mission) }}">Suivre</a></div>
            </article>
        @empty
            <div class="muted" style="padding:18px 0">Vous n’avez pas encore de mission. Créez votre première demande pour commencer.</div>
        @endforelse
        <div class="mt-4">{{ $missions->links() }}</div>
    </section>
@endsection
