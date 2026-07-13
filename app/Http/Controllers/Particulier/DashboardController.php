<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Récupérer les missions du particulier connecté
        $missions = Mission::where('particulier_id', $user->id)
            ->with(['artisan.user', 'devis', 'paiement'])
            ->latest()
            ->paginate(10);
        
        // Statistiques
        $stats = [
            'total' => Mission::where('particulier_id', $user->id)->count(),
            'en_attente' => Mission::where('particulier_id', $user->id)
                ->where('statut', 'en_attente')
                ->count(),
            'en_cours' => Mission::where('particulier_id', $user->id)
                ->whereIn('statut', ['affectee', 'acceptee', 'en_cours'])
                ->count(),
            'terminees' => Mission::where('particulier_id', $user->id)
                ->whereIn('statut', ['terminee', 'payee'])
                ->count(),
            'annulees' => Mission::where('particulier_id', $user->id)
                ->where('statut', 'annulee')
                ->count(),
        ];
        
        return view('particulier.dashboard', compact('missions', 'stats'));
    }
}