<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Notifications\DemandeCreeeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Intervention\Image\ImageManager;

class DemandeController extends Controller implements HasMiddleware
{
    // 🔒 Rate Limiting : 5 demandes toutes les 10 minutes
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:5,10'),
            new Middleware('auth'),
        ];
    }

    // 📝 Afficher le formulaire de demande
    public function create()
    {
        // 🔒 Vérifier que l'utilisateur est un customer (particulier)
        // ✅ CORRECTION : 'customer' au lieu de 'particulier'
        if (auth()->user()->role !== 'customer') {
            abort(403, 'Seuls les particuliers peuvent créer des demandes.');
        }

        // 🛠️ Vérifier s'il y a une demande en attente dans la session
        $missionEnCours = session('mission_en_cours');
        if ($missionEnCours) {
            $mission = Mission::find($missionEnCours);
            if ($mission && $mission->peutEtreAnnulee()) {
                return redirect()->route('particulier.mission.suivi', $mission)
                    ->with('info', 'Vous avez déjà une demande en cours.');
            }
        }

        $metiers = config('artilo.metiers');
        return view('particulier.demande.create', compact('metiers'));
    }

    // 📝 Enregistrer la demande
    public function store(Request $request)
    {
        // 🔒 Validation renforcée
        $validated = $request->validate([
            'metier_requis' => 'required|string|in:' . implode(',', config('artilo.metiers')),
            'description' => 'required|string|max:1000|regex:/^[a-zA-Z0-9\s\-\_\.\,\!\?\'\p{L}]+$/u',
            'adresse' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\_\.\,\!\?\'\p{L}]+$/u',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_souhaitee' => 'nullable|date|after_or_equal:today',
            'photos.*' => 'nullable|image|max:' . config('artilo.mission.max_photo_size', 5120) . '|mimes:jpeg,png,jpg,gif',
        ]);

        // 📸 Upload des photos
        $photos = [];
        if ($request->hasFile('photos')) {
            $maxPhotos = config('artilo.mission.max_photos', 5);
            foreach ($request->file('photos') as $index => $photo) {
                if ($index >= $maxPhotos) break;
                
                // Compression de l'image (amélioration)
                $image = ImageManager::gd()->read($photo);
                $image->scale(width: 800);
                $path = 'missions/' . uniqid() . '.jpg';
                Storage::disk('public')->put($path, (string) $image->encode());
                $photos[] = $path;
            }
        }

        // 📝 Création de la mission
        try {
            $mission = Mission::create([
                'particulier_id' => auth()->id(),
                'metier_requis' => $validated['metier_requis'],
                'description' => $validated['description'],
                'adresse' => $validated['adresse'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'date_souhaitee' => $validated['date_souhaitee'] ?? null,
                'photos' => $photos,
                'statut' => 'en_attente',
            ]);

            // 🛠️ Sauvegarder l'ID en session pour reprise
            session(['mission_en_cours' => $mission->id]);

            // 📧 Notification de création
            auth()->user()->notify(new DemandeCreeeNotification($mission));

            // 📝 Log de l'action
            Log::info('Nouvelle demande de service créée', [
                'mission_id' => $mission->id,
                'particulier_id' => auth()->id(),
                'metier' => $mission->metier_requis,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // ✅ Redirection avec message de succès
            return redirect()
                ->route('particulier.mission.suivi', $mission)
                ->with('success', 'Votre demande a été envoyée ! Nous recherchons un artisan pour vous.')
                ->with('notification', [
                    'message' => 'Nous recherchons un artisan pour vous. Vous serez notifié dans les prochaines minutes.',
                    'type' => 'success',
                    'timeout' => 5000
                ]);

        } catch (\Exception $e) {
            // 🔒 En cas d'erreur, supprimer les photos uploadées
            foreach ($photos as $photo) {
                Storage::disk('public')->delete($photo);
            }

            Log::error('Erreur lors de la création de la mission', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de votre demande. Veuillez réessayer.');
        }
    }

    // 📝 Afficher le suivi de la mission
    public function suivi(Mission $mission)
    {
        // 🔒 Vérifier que la mission appartient au particulier
        if ($mission->particulier_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette mission.');
        }

        return view('particulier.mission.suivi', compact('mission'));
    }

    // ❌ Annuler une mission
    public function annuler(Mission $mission)
    {
        // 🔒 Vérifier que la mission appartient au particulier
        if ($mission->particulier_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à annuler cette mission.');
        }

        // 🔒 Vérifier que la mission peut être annulée
        if (!$mission->peutEtreAnnulee()) {
            return back()->with('error', 'Cette mission ne peut plus être annulée.');
        }

        $mission->update(['statut' => 'annulee']);
        
        // 🧹 Nettoyer la session
        session()->forget('mission_en_cours');

        Log::info('Mission annulée par le particulier', [
            'mission_id' => $mission->id,
            'particulier_id' => auth()->id(),
        ]);

        return redirect()
            ->route('particulier.dashboard')
            ->with('success', 'Votre demande a été annulée avec succès.');
    }
}