<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Http\Request;

class ParticulierController extends Controller
{
    /**
     * Dashboard - Liste de tous les particuliers
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount(['missionsAsClient' => function ($q) {
                $q->whereIn('statut', ['en_attente', 'affectee', 'acceptee', 'en_cours']);
            }])
            ->withCount(['missionsAsClient as total_missions'])
            ->withCount(['missionsAsClient as completed_missions' => function ($q) {
                $q->whereIn('statut', ['terminee_prestataire', 'validee_client', 'payee']);
            }]);

        // Filtres
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->sort === 'missions') {
            $query->orderByDesc('total_missions');
        } elseif ($request->sort === 'recent') {
            $query->latest();
        } else {
            $query->latest();
        }

        $particuliers = $query->paginate(20)->withQueryString();

        // Statistiques globales
        $stats = [
            'total' => User::where('role', 'customer')->count(),
            'actifs' => User::where('role', 'customer')
                ->whereHas('missionsAsClient')
                ->count(),
            'nouveaux_mois' => User::where('role', 'customer')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_demandes' => Mission::count(),
            'demandes_en_cours' => Mission::whereIn('statut', ['en_attente', 'affectee', 'acceptee', 'en_cours'])->count(),
        ];

        return view('admin.particuliers.index', compact('particuliers', 'stats'));
    }

    /**
     * Fiche détaillée d'un particulier
     */
    public function show(User $user)
    {
        abort_if($user->role !== 'customer', 404);

        // Missions du particulier
        $missions = $user->missionsAsClient()
            ->with(['artisan.user', 'metier'])
            ->latest()
            ->paginate(10);

        // Statistiques individuelles
        $stats = [
            'total_missions' => $user->missionsAsClient()->count(),
            'missions_en_cours' => $user->missionsAsClient()
                ->whereIn('statut', ['en_attente', 'affectee', 'acceptee', 'en_cours', 'diagnostic_effectue'])
                ->count(),
            'missions_terminees' => $user->missionsAsClient()
                ->whereIn('statut', ['terminee_prestataire', 'validee_client', 'payee'])
                ->count(),
            'missions_annulees' => $user->missionsAsClient()
                ->where('statut', 'annulee')
                ->count(),
            'depenses_totales' => $user->missionsAsClient()
                ->whereIn('statut', ['payee', 'validee_client'])
                ->sum('budget_previsionnel'),
            'prestataires_utilises' => Artisan::whereHas('missions', function ($q) use ($user) {
                $q->where('particulier_id', $user->id);
            })->count(),
        ];

        // Dernière activité
        $derniereActivite = $user->missionsAsClient()->latest()->first();

        return view('admin.particuliers.show', compact('user', 'missions', 'stats', 'derniereActivite'));
    }

    /**
     * Missions d'un particulier (JSON pour filtrage)
     */
    public function missions(User $user, Request $request)
    {
        abort_if($user->role !== 'customer', 404);

        $query = $user->missionsAsClient()->with(['artisan.user', 'metier']);

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        $missions = $query->latest()->paginate(20);

        return view('admin.particuliers.missions', compact('user', 'missions'));
    }

    /**
     * Désactiver/Réactiver un compte particulier
     */
    public function toggleStatus(User $user)
    {
        abort_if($user->role !== 'customer', 404);

        // Logique de désactivation (à adapter selon votre système)
        // $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'Statut du particulier mis à jour.');
    }

    /**
     * Export CSV des particuliers
     */
    public function export()
    {
        $particuliers = User::where('role', 'customer')
            ->withCount('missionsAsClient')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="particuliers_'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($particuliers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nom', 'Email', 'Téléphone', 'Ville', 'Inscription', 'Missions', 'Dernière activité']);

            foreach ($particuliers as $p) {
                fputcsv($file, [
                    $p->name,
                    $p->email,
                    $p->phone_number ?? '—',
                    $p->city ?? '—',
                    $p->created_at->format('d/m/Y'),
                    $p->missions_as_client_count ?? 0,
                    $p->missionsAsClient()->latest()->first()?->created_at?->format('d/m/Y') ?? '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
