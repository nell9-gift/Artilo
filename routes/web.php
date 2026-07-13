<?php

// ======================================================
// IMPORTS
// ======================================================
// Contrôleurs et classes utilisés dans les routes ci-dessous
use App\Http\Controllers\Admin\ArtisanValidationController;
use App\Http\Controllers\Admin\ProfilArtisanController;
use App\Http\Controllers\Admin\MetierController;
use App\Http\Controllers\Artisan\ProfilController;
use App\Http\Controllers\Auth\RegisterArtisanController;
use App\Http\Controllers\Auth\RegisterCustomerController;
use App\Http\Controllers\Particulier\DemandeController;
use App\Http\Controllers\ProfileController;
use App\Models\Artisan;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\AttributionController;
use App\Http\Controllers\Artisan\MissionController as ArtisanMissionController;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ReaffecterMissionsExpirees;
use App\Http\Controllers\Artisan\DashboardController;
// ======================================================
// PAGE D'ACCUEIL
// ======================================================
Route::get('/', function () {
    return view('welcome');
});

// ======================================================
// ROUTES POUR LES PARTICULIERS (NOUVEAU)
// ======================================================
Route::middleware(['auth'])->prefix('particulier')->name('particulier.')->group(function () {
    
    // Dashboard particulier (redirige vers MaPage)
    Route::get('/dashboard', function () {
        return redirect()->route('MaPage');
    })->name('dashboard');
    
    // Demande de service
    Route::get('/demande/create', [DemandeController::class, 'create'])
        ->name('demande.create');
    Route::post('/demande', [DemandeController::class, 'store'])
        ->name('demande.store');
    Route::get('/demande/{mission}', [DemandeController::class, 'suivi'])
        ->name('mission.suivi');
    Route::post('/demande/{mission}/annuler', [DemandeController::class, 'annuler'])
        ->name('mission.annuler');

    // Rafraîchissement du token CSRF (évite l'erreur 419 sur les formulaires longs)
    Route::get('/csrf-refresh', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf-refresh');
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
Route::get('/artisan/MaPage', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('artisan.MaPage');

// ======================================================
// ROUTES ARTISAN
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
// GESTION DU PROFIL UTILISATEUR (Breeze)
// ======================================================
// Ces routes gèrent le profil de base fourni par Laravel Breeze,
// distinct du "profil artisan" métier géré au-dessus.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Charge les routes d'authentification générées par Laravel Breeze
// (login, logout, mot de passe oublié, vérification email, etc.)
require __DIR__.'/auth.php';

// ======================================================
// ROUTES D'INSCRIPTION
// ======================================================
// middleware('guest') : accessible uniquement aux visiteurs NON connectés
Route::middleware('guest')->group(function () {

    Route::get('/register', function () {
        return view('auth.register', ['activeTab' => 'customer']);
    })->name('register');

    Route::post('/register', [RegisterCustomerController::class, 'store'])
        ->name('register.customer.store');

    Route::get('/register/artisan', function () {
        return view('auth.register', ['activeTab' => 'artisan']);
    })->name('register.artisan');

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

            // --- Demandes (missions) en attente d'attribution à un prestataire ---
            // Colonne réelle : "statut" (enum), valeur "en_attente".
            // On utilise le scope scopeEnAttente() déjà défini dans le modèle Mission.
            $demandesEnAttente = Mission::enAttente()
                ->latest()
                ->paginate(10);

            // --- Missions affectées à un prestataire, en attente de sa réponse ---
            // Colonne réelle : "statut" (enum), valeur "affectee".
            // On utilise le scope scopeAffectees() déjà défini dans le modèle Mission.
            $missionsAffectees = Mission::affectees()
                ->latest()
                ->paginate(10);

            // --- Catalogue des métiers (Phase 8), avec le nombre de prestataires par métier ---
            $metiers = \App\Models\Metier::withCount('artisans')
                ->orderBy('nom')
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
                'areas',
                'demandesEnAttente',
                'missionsAffectees',
                'metiers'
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

            return back()->with('success', 'Photo de profil mise à jour.');
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
                'role' => 'customer',
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

// ============================================================
// ROUTES ADMIN - ATTRIBUTIONS
// ============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
    Route::get('/attributions/{mission}', [AttributionController::class, 'show'])->name('attributions.show');
    Route::post('/attributions/{mission}/attribuer', [AttributionController::class, 'attribuer'])->name('attributions.attribuer');
    Route::post('/attributions/{mission}/annuler', [AttributionController::class, 'annuler'])->name('attributions.annuler');
    Route::post('/attributions/{mission}/forcer', [AttributionController::class, 'forcerAttribution'])->name('attributions.forcer');
});

// ============================================================
// ROUTES ADMIN - CATALOGUE DES MÉTIERS (Phase 8)
// ============================================================
// NB : pas de ->name('admin.') sur ce groupe, car ->names('admin.metiers')
// ci-dessous gère déjà le préfixe de nommage. Cumuler les deux créerait
// des noms de route dupliqués (admin.admin.metiers.*).
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('metiers', MetierController::class)
        ->except(['show'])
        ->names('admin.metiers');
});

// ============================================================
// ROUTES PRESTATAIRE - MISSIONS
// ============================================================
Route::middleware(['auth', 'artisan'])->prefix('artisan')->name('artisan.')->group(function () {
    Route::get('/missions', [ArtisanMissionController::class, 'index'])->name('missions.index');
    Route::get('/missions/{mission}', [ArtisanMissionController::class, 'show'])->name('missions.show');
    Route::post('/missions/{mission}/accepter', [ArtisanMissionController::class, 'accepter'])->name('missions.accepter');
    Route::post('/missions/{mission}/refuser', [ArtisanMissionController::class, 'refuser'])->name('missions.refuser');
});
// Réaffectation automatique des missions expirées (toutes les minutes)
Schedule::command('artilo:reaffecter-missions')->everyMinute();

// Validation automatique des missions (Phase 18 - tous les jours à minuit)
Schedule::command('artilo:valider-missions-auto')->daily();

// Backup automatique (Phase 30 - tous les jours à 3h du matin)
Schedule::command('backup:run')->dailyAt('03:00');