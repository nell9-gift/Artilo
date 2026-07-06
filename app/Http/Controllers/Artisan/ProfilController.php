<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // Affiche le formulaire d'édition du profil
    public function edit()
    {
        $artisan = auth()->user()->artisan;
        return view('artisans.editprofil', compact('artisan'));  // ⬅️ MODIFIÉ
    }

    // Met à jour le profil
    public function update(Request $request)
    {
        $request->validate([
            'description'      => 'nullable|string|max:1000',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'address'          => 'nullable|string|max:255',
            'photos.*'         => 'nullable|image|max:2048',
        ]);

        $artisan = auth()->user()->artisan;
        $data = $request->only(['description', 'years_experience', 'address']);

        // Gestion des photos
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('artisans/photos', 'public');
                $photos[] = $path;
            }
            $data['photos'] = $photos;
        }

        $artisan->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
