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
            // Informations personnelles (User)
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',

            // Sécurité - Pièce d'identité
            'identity_document_type' => 'nullable|in:carte_nationale,passeport,permis',
            'identity_document_number' => 'nullable|string|max:50',
            'identity_expiration_date' => 'nullable|date',
            'identity_photo_recto' => 'nullable|image|max:2048',
            'identity_photo_verso' => 'nullable|image|max:2048',
            'identity_selfie' => 'nullable|image|max:2048',

            // Professionnel
            'main_profession' => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0|max:70',
            'sub_specialties_text' => 'nullable|string',
            'description' => 'nullable|string|max:2000',
            'company_name' => 'nullable|string|max:255',
            'company_registration_number' => 'nullable|string|max:50',
            'diplomas_text' => 'nullable|string',
            'certifications_text' => 'nullable|string',
            'photos.*' => 'nullable|image|max:2048',

            // Zone d'intervention
            'intervention_region' => 'nullable|string|max:255',
            'intervention_prefecture' => 'nullable|string|max:255',
            'intervention_city' => 'nullable|string|max:255',
            'max_distance_km' => 'nullable|integer|min:5|max:100',
            'artisan_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'availability_start_time' => 'nullable|date_format:H:i',
            'availability_end_time' => 'nullable|date_format:H:i|after:availability_start_time',
            'mobile_money_number' => 'nullable|string|max:20',
            'mobile_money_operator' => 'nullable|in:moov,yas',

            // Langues
            'languages' => 'nullable|array',
            'other_languages' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $artisan = $user->artisan;

        // Mettre à jour les champs User
        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'neighborhood' => $request->input('neighborhood'),
            'region' => $request->input('region'),
            'nationality' => $request->input('nationality'),
        ]);

        // Gestion de la photo de profil
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('users/profile-photos', 'public');
            $user->update(['profile_photo' => $path]);
        }

        // Préparer les données pour l'artisan
        $artisanData = $request->only([
            'main_profession',
            'years_experience',
            'description',
            'company_name',
            'company_registration_number',
            'identity_document_type',
            'identity_document_number',
            'identity_expiration_date',
            'intervention_region',
            'intervention_prefecture',
            'intervention_city',
            'max_distance_km',
            'latitude',
            'longitude',
            'availability_start_time',
            'availability_end_time',
            'mobile_money_number',
            'mobile_money_operator',
        ]);

        // Traiter l'adresse artisan
        if ($request->has('artisan_address')) {
            $artisanData['address'] = $request->input('artisan_address');
        }

        // Convertir les textes multi-lignes en arrays
        if ($request->filled('sub_specialties_text')) {
            $artisanData['sub_specialties'] = array_filter(
                array_map('trim', explode("\n", $request->input('sub_specialties_text')))
            );
        }

        if ($request->filled('diplomas_text')) {
            $artisanData['diplomas'] = array_filter(
                array_map('trim', explode("\n", $request->input('diplomas_text')))
            );
        }

        if ($request->filled('certifications_text')) {
            $artisanData['certifications'] = array_filter(
                array_map('trim', explode("\n", $request->input('certifications_text')))
            );
        }

        // Traiter les langues
        $languages = $request->input('languages', []);
        if ($request->filled('other_languages')) {
            $otherLangs = array_filter(
                array_map('trim', explode(',', $request->input('other_languages')))
            );
            $languages = array_merge($languages, $otherLangs);
        }
        if (! empty($languages)) {
            $artisanData['languages'] = $languages;
        }

        // Gestion des photos de pièce d'identité
        if ($request->hasFile('identity_photo_recto')) {
            if ($artisan->identity_photo_recto) {
                Storage::disk('public')->delete($artisan->identity_photo_recto);
            }
            $path = $request->file('identity_photo_recto')->store('artisans/identity', 'public');
            $artisanData['identity_photo_recto'] = $path;
        }

        if ($request->hasFile('identity_photo_verso')) {
            if ($artisan->identity_photo_verso) {
                Storage::disk('public')->delete($artisan->identity_photo_verso);
            }
            $path = $request->file('identity_photo_verso')->store('artisans/identity', 'public');
            $artisanData['identity_photo_verso'] = $path;
        }

        if ($request->hasFile('identity_selfie')) {
            if ($artisan->identity_selfie) {
                Storage::disk('public')->delete($artisan->identity_selfie);
            }
            $path = $request->file('identity_selfie')->store('artisans/identity', 'public');
            $artisanData['identity_selfie'] = $path;
        }

        // Gestion des photos de réalisations
        if ($request->hasFile('photos')) {
            $newPhotos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('artisans/photos', 'public');
                $newPhotos[] = $path;
            }
            $currentPhotos = $artisan->photos ?? [];
            $artisanData['photos'] = array_merge($currentPhotos, $newPhotos);
        }

        $artisan->update($artisanData);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
