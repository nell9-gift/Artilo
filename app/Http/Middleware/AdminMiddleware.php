<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté
        if (!auth()->check()) {
            abort(403, 'Accès refusé');
        }

        // Si l'utilisateur n'est pas admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }

        return $next($request);
    }
}