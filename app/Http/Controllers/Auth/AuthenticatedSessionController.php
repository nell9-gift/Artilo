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

        // Redirige vers le tableau de bord
        return redirect()->route('dashboard');
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
