<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ArtisanValide
{
    public function handle(Request $request, Closure $next)
    {
        // Récupère l'utilisateur connecté
        $user = $request->user();

        // Vérifie si un utilisateur existe et si son rôle est "artisan"
        if ($user && $user->role === 'artisan') {

            // Récupère le profil artisan associé à l'utilisateur
            $artisan = $user->artisan;

            // Vérifie si le profil artisan n'existe pas
            // ou si son statut n'est pas "valide"
            if (!$artisan || $artisan->statut !== 'valide') {

                // Redirige vers la page "en attente"
                return redirect()->route('artisan.en_attente');
            }
        }

        // Autorise la poursuite de la requête
        return $next($request);
    }
}