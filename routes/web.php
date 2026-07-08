<?php

// ======================================================
// IMPORTS
// ======================================================
// Contrôleurs et classes utilisés dans les routes ci-dessous
use App\Http\Controllers\Admin\ArtisanValidationController;
use App\Http\Controllers\Admin\ProfilArtisanController;  // ⬅️ NOUVEAU : gestion des profils artisans côté admin
use App\Http\Controllers\Artisan\ProfilController;       // ⬅️ NOUVEAU : édition du profil par l'artisan lui-même
use App\Http\Controllers\Auth\RegisterArtisanController;
use App\Http\Controllers\Auth\RegisterCustomerController;
use App\Http\Controllers\ProfileController;
use App\Models\Artisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite; // Connexion via Google (OAuth)

// ======================================================
// PAGE D'ACCUEIL
// ======================================================
Route::get('/', function () {
    return view('welcome');
});

// ======================================================
// TABLEAU DE BORD PRINCIPAL ("MaPage")
// ======================================================
// Point d'entrée commun après connexion.
// Redirige chaque utilisateur vers la vue correspondant à son rôle
// (admin, artisan) ou affiche la vue par défaut (client/customer).
Route::get('/MaPage', function () {
    $user = Auth::user();

    if ($user?->role === 'admin') {
        return redirect()->route('admin.MaPage');
    }

    if ($user?->role === 'artisan') {
        return redirect()->route('artisan.MaPage');
    }

    // Par défaut : vue destinée au client (customer)
    return view('MaPage');
})->middleware(['auth', 'verified'])->name('MaPage');

// Tableau de bord spécifique à l'artisan connecté
Route::get('/artisan/MaPage', function () {
    // Récupère la fiche "Artisan" liée au compte utilisateur connecté
    $artisan = Auth::user()->artisan;

    return view('artisans.MaPage', compact('artisan'));
})->middleware(['auth', 'verified'])->name('artisan.MaPage');

// ======================================================
// ROUTES ARTISAN (espace protégé)  ⬅️ NOUVEAU BLOC
// ======================================================
// Toutes les routes ici sont préfixées par /artisan et nommées artisan.*
// Réservées aux utilisateurs authentifiés (pas de vérification de rôle ici,
// à ajouter si besoin de restreindre strictement aux artisans).
Route::middleware(['auth'])->prefix('artisan')->name('artisan.')->group(function () {
    // Formulaire de modification du profil artisan (GET)
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');

    // Enregistrement des modifications du profil artisan (PUT)
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
});

// ======================================================
// GESTION DU PROFIL UTILISATEUR (compte générique Breeze)
// ======================================================
// Ces routes gèrent le profil de base fourni par Laravel Breeze,
// distinct du "profil artisan" métier géré au-dessus.
Route::middleware('auth')->group(function () {

    // Afficher le formulaire de profil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Mettre à jour les informations du profil
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Supprimer le compte utilisateur
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Charge les routes d'authentification générées par Laravel Breeze
// (login, logout, mot de passe oublié, vérification email, etc.)
require __DIR__.'/auth.php';

// ======================================================
// ROUTES D'INSCRIPTION
// ======================================================
// middleware('guest') : accessible uniquement aux visiteurs NON connectés
Route::middleware('guest')->group(function () {

    // ---------- Inscription d'un particulier (customer) ----------

    // Affiche le formulaire d'inscription avec l'onglet "particulier" actif
    Route::get('/register', function () {
        return view('auth.register', ['activeTab' => 'customer']);
    })->name('register');

    // Traite la soumission du formulaire d'inscription du particulier
    Route::post('/register', [RegisterCustomerController::class, 'store'])
        ->name('register.customer.store');

    // ---------- Inscription d'un artisan ----------

    // Affiche le même formulaire mais avec l'onglet "Artisan" actif
    Route::get('/register/artisan', function () {
        return view('auth.register', ['activeTab' => 'artisan']);
    })->name('register.artisan');

    // Traite la soumission du formulaire d'inscription de l'artisan
    Route::post('/register/artisan', [RegisterArtisanController::class, 'store'])
        ->name('register.artisan.store');
});

// ======================================================
// ROUTES D'ADMINISTRATION
// ======================================================
// Accès restreint : utilisateur connecté ('auth') ET ayant le rôle admin ('admin')
// Toutes les routes sont préfixées /admin et nommées admin.*
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Tableau de bord admin : calcule toutes les statistiques
        // affichées sur la page (compteurs, derniers artisans, etc.)
        Route::get('/MaPage', function () {
            // --- Compteurs par statut de validation des artisans ---
            $pendingCount = Artisan::where('status', 'pending')->count();
            $approvedCount = Artisan::where('status', 'approved')->count();
            $rejectedCount = Artisan::where('status', 'rejected')->count();
            $totalArtisans = Artisan::count();

            // --- Compteurs utilisateurs ---
            $totalUsers = User::count();
            $customerCount = User::where('role', 'customer')->count();
            $adminCount = User::where('role', 'admin')->count();

            // Nombre d'artisans ayant au moins commencé à remplir leur profil
            // (description OU photos renseignées)
            $profileCount = Artisan::where(function ($query) {
                $query->whereNotNull('description')
                    ->orWhereNotNull('photos');
            })->count();

            // Nombre d'artisans dont le profil est incomplet
            // (il manque au moins un champ clé)
            $incompleteProfilesCount = Artisan::where(function ($query) {
                $query->whereNull('identity_document')
                    ->orWhereNull('description')
                    ->orWhereNull('address')
                    ->orWhereNull('photos');
            })->count();

            // Les 5 dernières candidatures en attente de validation
            $latestCandidates = Artisan::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            // Les 5 derniers artisans inscrits, tous statuts confondus
            $latestArtisans = Artisan::with('user')
                ->latest()
                ->take(5)
                ->get();

            // Top 5 des métiers (professions) les plus représentés
            $professions = Artisan::selectRaw('profession, count(*) as total')
                ->groupBy('profession')
                ->orderByDesc('total')
                ->take(5)
                ->get();

            // Top 5 des zones d'intervention les plus représentées
            $areas = Artisan::selectRaw('intervention_area, count(*) as total')
                ->groupBy('intervention_area')
                ->orderByDesc('total')
                ->take(5)
                ->get();

            // Envoie toutes ces variables à la vue admin.MaPage
            return view('admin.MaPage', compact(
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'totalArtisans',
                'totalUsers',
                'customerCount',
                'adminCount',
                'profileCount',
                'incompleteProfilesCount',
                'latestCandidates',
                'latestArtisans',
                'professions',
                'areas'
            ));
        })->name('MaPage');

        // Met a jour la photo de profil de l'administrateur connecte.
        Route::post('/profile-photo', function (Request $request) {
            $request->validate([
                'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            $user = $request->user();

            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            $user->update([
                'profile_photo' => $path,
            ]);

            return back()->with('success', 'Photo de profil mise a jour.');
        })->name('profile-photo.update');

        // Liste des artisans en attente de validation par l'admin
        Route::get('/artisans', [ArtisanValidationController::class, 'index'])
            ->name('artisans.index');

        // Affiche/télécharge le document d'identité fourni par un artisan
        Route::get('/artisans/{artisan}/document', [ArtisanValidationController::class, 'voirDocument'])
            ->name('artisans.document');

        // Valide définitivement le compte d'un artisan (status => approved)
        Route::post('/artisans/{artisan}/valider', [ArtisanValidationController::class, 'valider'])
            ->name('artisans.valider');

        // Refuse le compte d'un artisan (status => rejected)
        Route::post('/artisans/{artisan}/refuser', [ArtisanValidationController::class, 'refuser'])
            ->name('artisans.refuser');

        // ======================================================
        // GESTION DES PROFILS DES ARTISANS  ⬅️ NOUVEAU
        // ======================================================
        // Distinct de la validation de compte ci-dessus : ici on gère
        // le contenu du profil (description, photos, etc.) une fois le
        // compte déjà validé.

        // Liste des artisans ayant rempli leur profil
        Route::get('/profils', [ProfilArtisanController::class, 'index'])
            ->name('profils.index');

        // Détail du profil d'un artisan en particulier
        Route::get('/profils/{artisan}', [ProfilArtisanController::class, 'show'])
            ->name('profils.show');
    });

// ======================================================
// AUTHENTIFICATION AVEC GOOGLE (OAuth via Socialite)
// ======================================================

// Étape 1 : redirige l'utilisateur vers l'écran de connexion Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')->stateless()->redirect();
})->name('auth.google');

// Étape 2 : callback appelé par Google après authentification réussie
Route::get('/auth/google/callback', function () {
    try {
        // Récupère les infos du compte Google (nom, email, etc.)
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Cherche si un utilisateur existe déjà avec cet email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Si aucun compte trouvé : création automatique d'un nouveau compte
        // NB : mot de passe aléatoire car non utilisé (connexion via Google uniquement)
        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                'role' => 'customer', // rôle par défaut attribué aux inscriptions Google
            ]);
        }

        // Connecte l'utilisateur (nouveau ou existant) dans la session
        Auth::login($user);

        // Redirection selon le rôle, comme pour une connexion classique
        if ($user->role === 'admin') {
            return redirect()->route('admin.MaPage');
        }

        return redirect()->route('MaPage');

    } catch (Exception $e) {
        // En cas d'échec (ex: refus de l'utilisateur, erreur API Google)
        // on revient à la page de connexion avec un message d'erreur
        return redirect('/login')->with(
            'error',
            'Erreur lors de la connexion Google. Veuillez réessayer.'
        );
    }
})->name('auth.google.callback');

// ======================================================
// ROUTES PUBLIQUES
// ======================================================

// Page de découverte des artisans, accessible sans connexion
// Utilisée par le bouton "Explorer les artisans" sur le site
Route::get('/artisans', function () {
    return view('artisans.index');
});
