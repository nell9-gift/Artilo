<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté et a le rôle 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Sinon, rediriger vers le dashboard (ou une autre page)
        return redirect('/dashboard')->with('error', 'Accès refusé : vous n\'êtes pas administrateur.');
    }
}
