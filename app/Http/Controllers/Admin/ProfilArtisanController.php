<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use Illuminate\Http\Request;

class ProfilArtisanController extends Controller
{
    // Liste tous les artisans qui ont rempli leur profil
    public function index()
    {
        $artisans = Artisan::with('user')
            ->whereNotNull('description')
            ->orWhereNotNull('photos')
            ->latest()
            ->paginate(20);

        return view('admin.profils.index', compact('artisans'));
    }

    // Voir le détail d'un profil d'artisan
    public function show(Artisan $artisan)
    {
        return view('admin.profils.show', compact('artisan'));
    }
}