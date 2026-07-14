<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtisanMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check() || Auth::user()->role !== 'artisan') {
            abort(403, 'Accès réservé aux artisans.');
        }

        return $next($request);
    }
}
