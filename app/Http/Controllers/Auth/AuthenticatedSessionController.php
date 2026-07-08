<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // Affiche la page de connexion
    public function create(): View
    {
        return view('auth.login');
    }

    // Traite la demande de connexion
    public function store(LoginRequest $request): RedirectResponse
    {
        // Vérifie les identifiants de l'utilisateur
        $request->authenticate();

        // Régénère la session pour renforcer la sécurité
        $request->session()->regenerate();

        // Récupère l'utilisateur actuellement connecté
        $user = Auth::user();

        // Si c'est un admin, direction le panneau admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.MaPage');
        }

        // Si c'est un artisan, on vérifie que son dossier est validé
        if ($user->role === 'artisan') {
            $artisan = $user->artisan;

            if ($artisan && $artisan->status === 'pending') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Votre candidature est toujours en cours d\'etude. Vous recevrez un email des qu\'elle sera validee.');
            }

            if ($artisan && $artisan->status === 'rejected') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Votre candidature n\'a pas ete retenue. Contactez-nous pour plus d\'informations.');
            }
        }

        // Sinon (customer, ou artisan validé), direction le tableau de bord
        return redirect()->intended(route('MaPage', absolute: false));
    }

    // Déconnecte l'utilisateur
    public function destroy(Request $request): RedirectResponse
    {
        // Ferme la session de l'utilisateur connecté
        Auth::guard('web')->logout();

        // Invalide l'ancienne session
        $request->session()->invalidate();

        // Génère un nouveau jeton CSRF
        $request->session()->regenerateToken();

        // Redirige vers la page d'accueil
        return redirect('/');
    }
}
