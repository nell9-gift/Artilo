<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterArtisanController extends Controller
{
    // Affiche le formulaire d'inscription des artisans
    public function create()
    {
        return view('auth.register-artisan');
    }

    // Traite les donnÃ©es soumises par le formulaire d'inscription
    public function store(Request $request)
    {
        // Validation des donnÃ©es saisies par l'utilisateur
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'required|string|max:20',
            'password' => 'required|confirmed|min:8',
            'profession' => 'required|string',
            'intervention_area' => 'required|string',
            'identity_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        // CrÃ©ation du compte utilisateur avec le rÃ´le "artisan"
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password), // Chiffrement du mot de passe
            'role' => 'artisan',
        ]);
        // Enregistre le document d'identitÃ©dans le stockage privÃ©
        $documentPath = $request->file('identity_document')
            ->store('identity_documents', 'private');
        // CrÃ©e le profil de l'artisan associÃ© Ã  l'utilisateur
        Artisan::create([
            'user_id' => $user->id,
            'profession' => $request->profession,
            'intervention_area' => $request->intervention_area,
            'identity_document' => $documentPath,
            'status' => 'pending', // En attente de validation
        ]);

        // Pas de connexion automatique : l'artisan doit attendre la validation admin
        return redirect()->route('login')
            ->with('status', 'Votre candidature a bien Ã©tÃ© envoyÃ©e ! Elle est en cours d\'Ã©tude par notre Ã©quipe. Vous recevrez un email dÃ¨s qu\'elle sera validÃ©e.');
    }
}
