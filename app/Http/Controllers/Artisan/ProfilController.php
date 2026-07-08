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
        return view('artisans.editprofil', compact('artisan'));
    }

    // Met à jour le profil
    public function update(Request $request)
    {
        $request->validate([
            'description'              => 'nullable|string|max:1000',
            'years_experience'         => 'nullable|integer|min:0|max:50',
            'address'                  => 'nullable|string|max:255',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'max_distance_km'          => 'nullable|integer|min:1|max:100',
            'availability_start_time'  => 'nullable|date_format:H:i',
            'availability_end_time'    => 'nullable|date_format:H:i|after:availability_start_time',
            'mobile_money_number'      => 'nullable|string|max:20',
            'mobile_money_operator'    => 'nullable|in:moov,yas',
            'photos.*'                 => 'nullable|image|max:2048',
        ]);

        $artisan = auth()->user()->artisan;
        $data = $request->only([
            'description',
            'years_experience',
            'address',
            'latitude',
            'longitude',
            'max_distance_km',
            'availability_start_time',
            'availability_end_time',
            'mobile_money_number',
            'mobile_money_operator',
        ]);

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
