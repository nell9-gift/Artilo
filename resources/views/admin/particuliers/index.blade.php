@extends('admin.layout')

@section('content')
<div class="p-6 max-w-7xl mx-auto" style="color: #1B1230;">
    
    <style>
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(124,58,237,0.12);
            transition: all 0.3s;
        }
        .stat-card:hover {
            box-shadow: 0 8px 32px -12px rgba(124,58,237,0.2);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #7C3AED, #6D28D9);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -8px rgba(124,58,237,0.4);
        }
        .search-input {
            width: 100%;
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid rgba(124,58,237,0.16);
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .search-input:focus {
            outline: none;
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6B5B95;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(124,58,237,0.1);
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(124,58,237,0.06);
            font-weight: 500;
        }
        tr:hover {
            background: rgba(124,58,237,0.03);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-success {
            background: rgba(5,150,105,0.12);
            color: #059669;
        }
        .badge-warning {
            background: rgba(217,119,6,0.12);
            color: #D97706;
        }
    </style>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="{{ route('admin.MaPage') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">/</span>
            <span class="font-semibold text-gray-700">Particuliers</span>
        </div>
        <h1 class="text-3xl font-black" style="color: #2E1065;">👥 Gestion des Particuliers</h1>
        <p class="text-gray-500 mt-1">Liste de tous les clients inscrits sur la plateforme.</p>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-sm text-gray-500 font-semibold">Total</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm text-gray-500 font-semibold">Actifs</p>
            <p class="text-2xl font-black" style="color: #059669;">{{ $stats['actifs'] }}</p>
            <p class="text-xs text-gray-400">ont fait ≥1 demande</p>
        </div>
        <div class="stat-card">
            <p class="text-sm text-gray-500 font-semibold">Nouveaux (mois)</p>
            <p class="text-2xl font-black" style="color: #7C3AED;">{{ $stats['nouveaux_mois'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm text-gray-500 font-semibold">Demandes totales</p>
            <p class="text-2xl font-black" style="color: #D97706;">{{ $stats['total_demandes'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm text-gray-500 font-semibold">En cours</p>
            <p class="text-2xl font-black" style="color: #2563EB;">{{ $stats['demandes_en_cours'] }}</p>
        </div>
    </div>

    {{-- Barre de recherche et actions --}}
    <div class="bg-white rounded-2xl p-4 mb-6 border" style="border-color: rgba(124,58,237,0.12);">
        <div class="flex flex-wrap items-center gap-3 justify-between">
            <form method="GET" class="flex-1 max-w-md">
                <input 
                    type="search" 
                    name="search" 
                    placeholder="Rechercher par nom, email, téléphone..." 
                    class="search-input"
                    value="{{ request('search') }}"
                >
            </form>
            <div class="flex gap-2">
                <a href="{{ route('admin.particuliers.export') }}" class="btn-primary" style="background: #fff; color: #7C3AED; border: 1px solid rgba(124,58,237,0.2);">
                    📥 Exporter CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border overflow-hidden" style="border-color: rgba(124,58,237,0.12);">
        <table>
            <thead>
                <tr>
                    <th>Particulier</th>
                    <th>Contact</th>
                    <th>Localisation</th>
                    <th>Inscription</th>
                    <th>Missions</th>
                    <th>Activité</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($particuliers as $particulier)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                                     style="background: linear-gradient(135deg, #7C3AED, #D97706);">
                                    {{ strtoupper(substr($particulier->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $particulier->name }}</p>
                                    <p class="text-xs text-gray-400">ID: #{{ $particulier->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-sm text-gray-700">{{ $particulier->email }}</p>
                            <p class="text-xs text-gray-400">{{ $particulier->phone_number ?? '—' }}</p>
                        </td>
                        <td>
                            <p class="text-sm text-gray-700">{{ $particulier->city ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $particulier->neighborhood ?? '' }}</p>
                        </td>
                        <td>
                            <span class="text-sm">{{ $particulier->created_at->format('d/m/Y') }}</span>
                            <p class="text-xs text-gray-400">{{ $particulier->created_at->diffForHumans() }}</p>
                        </td>
                        <td>
                            <span class="badge badge-warning">
                                📋 {{ $particulier->total_missions }} mission(s)
                            </span>
                            @if($particulier->missions_as_client_count > 0)
                                <p class="text-xs text-blue-600 mt-1">{{ $particulier->missions_as_client_count }} en cours</p>
                            @endif
                        </td>
                        <td>
                            @if($particulier->total_missions > 0)
                                <span class="badge badge-success">✅ Actif</span>
                            @else
                                <span class="text-sm text-gray-400">Aucune activité</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.particuliers.show', $particulier) }}" 
                               class="text-sm font-bold hover:underline" 
                               style="color: #7C3AED;">
                                Voir détails →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <p class="text-4xl mb-2">🔍</p>
                            <p class="font-bold text-gray-500">Aucun particulier trouvé</p>
                            <p class="text-sm text-gray-400">Essayez de modifier vos critères de recherche.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $particuliers->links() }}
    </div>
</div>
@endsection