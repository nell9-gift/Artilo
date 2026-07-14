@extends('admin.layout')

@section('content')
<div class="p-6 max-w-7xl mx-auto" style="color: #1B1230;">
    
    <style>
        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(124,58,237,0.12);
            margin-bottom: 24px;
        }
        .stat-mini {
            text-align: center;
            padding: 16px;
            border-radius: 12px;
            background: rgba(124,58,237,0.04);
        }
        .timeline-item {
            padding: 16px;
            border-left: 3px solid #7C3AED;
            margin-bottom: 12px;
            background: rgba(124,58,237,0.02);
            border-radius: 0 8px 8px 0;
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.MaPage') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('admin.particuliers.index') }}" class="text-gray-400 hover:text-gray-600">Particuliers</a>
        <span class="text-gray-300">/</span>
        <span class="font-semibold text-gray-700">{{ $user->name }}</span>
    </div>

    {{-- Header Profil --}}
    <div class="card">
        <div class="flex flex-wrap items-start gap-6">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white font-bold text-2xl"
                 style="background: linear-gradient(135deg, #7C3AED, #D97706);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-black">{{ $user->name }}</h1>
                <p class="text-gray-500 mt-1">
                    <span class="font-semibold">{{ $user->email }}</span>
                    @if($user->phone_number)
                        <span class="mx-2">•</span>
                        {{ $user->phone_number }}
                    @endif
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    Inscrit le {{ $user->created_at->format('d/m/Y') }}
                    <span class="mx-2">•</span>
                    Dernière activité: {{ $derniereActivite?->created_at?->diffForHumans() ?? 'Aucune' }}
                </p>
                @if($user->address)
                    <p class="text-sm text-gray-500 mt-2">📍 {{ $user->address }}, {{ $user->city ?? '' }} {{ $user->neighborhood ?? '' }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="stat-mini">
            <p class="text-2xl font-black">{{ $stats['total_missions'] }}</p>
            <p class="text-xs text-gray-500 font-semibold">Total missions</p>
        </div>
        <div class="stat-mini">
            <p class="text-2xl font-black" style="color: #2563EB;">{{ $stats['missions_en_cours'] }}</p>
            <p class="text-xs text-gray-500 font-semibold">En cours</p>
        </div>
        <div class="stat-mini">
            <p class="text-2xl font-black" style="color: #059669;">{{ $stats['missions_terminees'] }}</p>
            <p class="text-xs text-gray-500 font-semibold">Terminées</p>
        </div>
        <div class="stat-mini">
            <p class="text-2xl font-black" style="color: #DC2626;">{{ $stats['missions_annulees'] }}</p>
            <p class="text-xs text-gray-500 font-semibold">Annulées</p>
        </div>
        <div class="stat-mini">
            <p class="text-2xl font-black" style="color: #7C3AED;">
                {{ $stats['depenses_totales'] ? number_format($stats['depenses_totales'], 0, ',', ' ') . ' F' : '—' }}
            </p>
            <p class="text-xs text-gray-500 font-semibold">Dépenses</p>
        </div>
        <div class="stat-mini">
            <p class="text-2xl font-black" style="color: #D97706;">{{ $stats['prestataires_utilises'] }}</p>
            <p class="text-xs text-gray-500 font-semibold">Prestataires</p>
        </div>
    </div>

    {{-- Liste des missions --}}
    <div class="card">
        <h2 class="text-xl font-black mb-4">📋 Historique des missions</h2>
        
        @if($missions->isNotEmpty())
            <div class="space-y-3">
                @foreach($missions as $mission)
                    <div class="timeline-item">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold">#{{ $mission->id }}</span>
                                    <span class="badge-status"
                                          style="background: rgba(124,58,237,0.1); color: #7C3AED;">
                                        {{ $mission->metier_requis ?? ($mission->metier->nom ?? 'Non défini') }}
                                    </span>
                                    <span class="badge-status"
                                          style="background: {{ $mission->statut === 'payee' || $mission->statut === 'validee_client' ? 'rgba(5,150,105,0.1)' : ($mission->statut === 'annulee' ? 'rgba(220,38,38,0.1)' : 'rgba(37,99,235,0.1)') }}; 
                                                 color: {{ $mission->statut === 'payee' || $mission->statut === 'validee_client' ? '#059669' : ($mission->statut === 'annulee' ? '#DC2626' : '#2563EB') }};">
                                        {{ $mission->statut_libelle }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $mission->created_at->format('d/m/Y H:i') }}
                                    @if($mission->artisan)
                                        <span class="mx-1">•</span>
                                        Prestataire: <strong>{{ $mission->artisan->user->name ?? 'Prestataire' }}</strong>
                                    @endif
                                </p>
                                @if($mission->description)
                                    <p class="text-sm text-gray-600 mt-1 truncate max-w-lg">{{ $mission->description }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                @if($mission->budget_previsionnel)
                                    <p class="font-bold">{{ number_format($mission->budget_previsionnel, 0, ',', ' ') }} F</p>
                                @endif
                                @if($mission->adresse)
                                    <p class="text-xs text-gray-400">📍 {{ $mission->adresse }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $missions->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-4xl mb-2">📭</p>
                <p class="font-bold text-gray-500">Aucune mission</p>
                <p class="text-sm text-gray-400">Ce particulier n'a pas encore créé de demande.</p>
            </div>
        @endif
    </div>
</div>
@endsection