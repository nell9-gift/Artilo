<?php

// Import des contrôleurs et classes nécessaires
use App\Http\Controllers\Admin\ArtisanValidationController;
use App\Http\Controllers\Auth\RegisterArtisanController;
use App\Http\Controllers\Auth\RegisterCustomerController;
use App\Http\Controllers\ProfileController;
use App\Models\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

// Route de la page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Tableau de bord
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware('auth')->group(function () {

    // Afficher le profil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Mettre à jour le profil
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Supprimer le profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Routes d'authentification Laravel Breeze
require __DIR__.'/auth.php';
// ======================================================
// ROUTES D'INSCRIPTION
// ======================================================

Route::middleware('guest')->group(function () {
    // ---------- Inscription d'un particulier ----------

    // Affiche le formulaire d'inscription avec l'onglet particulier actif
    Route::get('/register', function () {
        return view('auth.register', ['activeTab' => 'customer']);
    })->name('register');

    // Traite les données du formulaire d'inscription du particulier
    Route::post('/register', [RegisterCustomerController::class, 'store'])
        ->name('register.customer.store');

    // ---------- Inscription d'un artisan ----------

    // Affiche le formulaire d'inscription avec l'onglet "Artisan" actif
    Route::get('/register/artisan', function () {
        return view('auth.register', ['activeTab' => 'artisan']);
    })->name('register.artisan');

    // Traite les données du formulaire d'inscription de l'artisan
    Route::post('/register/artisan', [RegisterArtisanController::class, 'store'])
        ->name('register.artisan.store');
});

// ======================================================
// ROUTES D'ADMINISTRATION
// ======================================================

// Toutes les routes de cette section sont accessibles
// uniquement aux utilisateurs connectés ayant le rôle d'administrateur.
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Tableau de bord admin
        Route::get('/dashboard', function () {
            $pendingCount = Artisan::where('status', 'pending')->count();

            return view('admin.dashboard', compact('pendingCount'));
        })->name('dashboard');

        // Affiche la liste des artisans en attente de validation
        Route::get('/artisans', [ArtisanValidationController::class, 'index'])
            ->name('artisans.index');

        // Affiche le document d'identité d'un artisan
        Route::get('/artisans/{artisan}/document', [ArtisanValidationController::class, 'voirDocument'])
            ->name('artisans.document');

        // Valide le compte d'un artisan
        Route::post('/artisans/{artisan}/valider', [ArtisanValidationController::class, 'valider'])
            ->name('artisans.valider');

        // Refuse le compte d'un artisan
        Route::post('/artisans/{artisan}/refuser', [ArtisanValidationController::class, 'refuser'])
            ->name('artisans.refuser');
    });

// ======================================================
// AUTHENTIFICATION AVEC GOOGLE
// ======================================================

// Redirige l'utilisateur vers la page de connexion Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')->stateless()->redirect();
})->name('auth.google');

// Callback exécuté après la connexion Google
Route::get('/auth/google/callback', function () {
    try {
        // Récupère les informations du compte Google
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Recherche un utilisateur ayant le même email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Si aucun utilisateur n'existe, on crée un nouveau compte
        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                'role' => 'customer', // rôle attribué par défaut
            ]);
        }

        // Connecte automatiquement l'utilisateur
        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Redirige vers le tableau de bord pour les autres rôles
        return redirect()->route('dashboard');

    } catch (Exception $e) {

        // En cas d'erreur, retour à la page de connexion
        return redirect('/login')->with(
            'error',
            'Erreur lors de la connexion Google. Veuillez réessayer.'
        );
    }
})->name('auth.google.callback');

// ======================================================
// ROUTES PUBLIQUES
// ======================================================

// Affiche la page de découverte des artisans
// Utilisée par le bouton "Explorer les artisans"
Route::get('/artisans', function () {
    return view('artisans.index');
});
