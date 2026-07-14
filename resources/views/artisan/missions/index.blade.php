@extends('artisan.layout', ['pageActive' => 'missions', 'pageTitle' => 'Mes missions', 'pageSubtitle' => 'Répondez aux missions proposées et suivez vos chantiers.'])

@section('content')
    @foreach(['Missions à répondre' => $missionsEnAttente, 'Missions en cours' => $missionsAcceptees, 'Historique' => $missionsTerminees] as $title => $collection)
        <section class="pro-card" style="padding:22px;margin-bottom:18px"><h2 style="font-size:18px;margin:0 0 8px">{{ $title }}</h2>@forelse($collection as $mission)<article class="pro-mission"><div><h3>{{ ucfirst($mission->metier_requis) }}</h3><p>{{ $mission->particulier?->name ?? 'Client' }} · {{ $mission->adresse }}</p></div><div style="display:flex;align-items:center;gap:10px"><span class="pro-badge">{{ $mission->statut_libelle }}</span><a class="pro-button secondary" href="{{ route('artisan.missions.show', $mission) }}">Détail</a></div></article>@empty<p style="color:var(--muted);margin:15px 0 0">Aucune mission dans cette catégorie.</p>@endforelse</section>
    @endforeach
@endsection
