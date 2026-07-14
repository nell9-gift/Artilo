@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('admin.prestataires.index') }}" class="text-purple-600 hover:underline mb-4 inline-block">← Retour à la liste</a>
    
    <h1 class="text-3xl font-bold mb-6">{{ $artisan->user->name ?? 'Prestataire' }}</h1>

    {{-- Infos --}}
    <div class="bg-white rounded-lg p-6 shadow mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div><strong>Email :</strong> {{ $artisan->user->email ?? '—' }}</div>
            <div><strong>Profession :</strong> {{ $artisan->profession ?? '—' }}</div>
            <div><strong>Zone :</strong> {{ $artisan->intervention_area ?? '—' }}</div>
            <div><strong>Niveau :</strong> {{ $artisan->niveau_libelle ?? '—' }}</div>
            <div><strong>Score :</strong> {{ $artisan->score_interne ?? '—' }}/100</div>
            <div><strong>Note :</strong> {{ $artisan->average_rating ?? '—' }}/5</div>
            <div><strong>Statut :</strong> {{ $artisan->statut_libelle ?? '—' }}</div>
            <div><strong>Disponible :</strong> {{ $artisan->est_disponible ? '✅ Oui' : '❌ Non' }}</div>
        </div>
        
        <div class="flex gap-3 mt-4">
            @if($artisan->status == 'pending')
                <form action="{{ route('admin.prestataires.valider', $artisan) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded">✅ Valider</button>
                </form>
            @endif
            <form action="{{ route('admin.prestataires.toggle-dispo', $artisan) }}" method="POST">
                @csrf
                <button class="bg-blue-600 text-white px-4 py-2 rounded">🔄 {{ $artisan->est_disponible ? 'Désactiver' : 'Activer' }}</button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 shadow text-center"><p class="text-2xl font-bold">{{ $stats['total_missions'] }}</p><p class="text-gray-500">Total missions</p></div>
        <div class="bg-white rounded-lg p-4 shadow text-center"><p class="text-2xl font-bold text-blue-600">{{ $stats['missions_acceptees'] }}</p><p class="text-gray-500">En cours</p></div>
        <div class="bg-white rounded-lg p-4 shadow text-center"><p class="text-2xl font-bold text-green-600">{{ $stats['missions_terminees'] }}</p><p class="text-gray-500">Terminées</p></div>
        <div class="bg-white rounded-lg p-4 shadow text-center"><p class="text-2xl font-bold text-red-600">{{ $stats['missions_refusees'] }}</p><p class="text-gray-500">Refusées</p></div>
    </div>

    {{-- Missions --}}
    <h2 class="text-2xl font-bold mb-4">📋 Missions</h2>
    @foreach($missions as $mission)
    <div class="bg-white rounded-lg p-4 shadow mb-3">
        <div class="flex justify-between">
            <div>
                <span class="font-bold">#{{ $mission->id }}</span>
                <span class="text-gray-600 ml-2">{{ $mission->particulier->name ?? 'Client' }}</span>
                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs ml-2">{{ $mission->statut_libelle }}</span>
            </div>
            <span class="text-sm text-gray-500">{{ $mission->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
    @endforeach
    
    {{ $missions->links() }}
</div>
@endsection