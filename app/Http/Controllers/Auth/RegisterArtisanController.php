<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegisterArtisanController extends Controller
{
    // Affiche le formulaire d'inscription des artisans
    public function create()
    {
        return view('auth.register-artisan');
    }

    // Traite les données soumises par le formulaire d'inscription
    public function store(Request $request)
    {
        // Validation des données saisies par l'utilisateur
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'required|string|max:20',
            'password' => 'required|confirmed|min:8',
            'profession' => 'required|string',
            'intervention_area' => 'required|string',
            'identity_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Création du compte utilisateur avec le rôle "artisan"
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password), // Chiffrement du mot de passe
            'role' => 'artisan',
        ]);

        // Enregistre le document d'identité dans le stockage privé
        $documentPath = $request->file('identity_document')
            ->store('identity_documents', 'private');

        // Crée le profil de l'artisan associé à l'utilisateur
        Artisan::create([
            'user_id' => $user->id,
            'profession' => $request->profession,
            'intervention_area' => $request->intervention_area,
            'identity_document' => $documentPath,
            'status' => 'pending', // En attente de validation
        ]);

        // Connecte automatiquement l'artisan après son inscription
        Auth::login($user);

        // Redirige vers le tableau de bord avec un message d'information
        return redirect()->route('dashboard')
            ->with('info', 'Your account is being validated by our team.');
    }
}