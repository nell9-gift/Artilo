<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterCustomerController extends Controller
{
    // Affiche la page du formulaire d'inscription client
    public function create()
    {
        return view('auth.register-customer');
    }

    // Traite les données envoyées par le formulaire
    public function store(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'name' => 'required|string|max:255',   // nom obligatoire
            'email' => 'required|email|unique:users', // email unique dans la table users
            'telephone' => 'required|string|max:20',     // numéro obligatoire
            'password' => 'required|confirmed|min:8',   // mot de passe + confirmation + min 8 caractères
        ]);

        // Création de l'utilisateur dans la base de données
        $user = User::create([
            'name' => $request->name,                  // nom
            'email' => $request->email,                 // email
            'telephone' => $request->telephone,             // téléphone
            'password' => Hash::make($request->password),  // mot de passe chiffré
            'role' => 'customer',                      // rôle = client
        ]);

        // Redirection vers la page de connexion après inscription
        return redirect()->route('login')
            ->with('status', 'Votre compte a bien été créé. Connectez-vous pour y accéder.');
    }
}
