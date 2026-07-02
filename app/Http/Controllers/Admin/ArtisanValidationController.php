<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use Illuminate\Http\Request;

// Contrôleur de validation des artisans
class ArtisanValidationController extends Controller
{
    // Afficher les artisans en attente
    public function index()
    {
        $artisans = Artisan::with('user')
            ->where('statut', 'en_attente')
            ->latest()
            ->paginate(20);

        return view('admin.artisans.index', compact('artisans'));
    }

    // Valider un artisan
    public function valider(Artisan $artisan)
    {
        $artisan->update(['statut' => 'valide']);

        // Notification à ajouter plus tard
        return back()->with('success', "Artisan {$artisan->user->name} validé.");
    }

    // Refuser un artisan
    public function refuser(Request $request, Artisan $artisan)
    {
        // Vérifier le motif du refus
        $request->validate([
            'motif_refus' => 'required|string|max:500'
        ]);

        // Mettre à jour le statut et enregistrer le motif
        $artisan->update([
            'statut'      => 'refuse',
            'motif_refus' => $request->motif_refus,
        ]);

        return back()->with('success', "Artisan refusé.");
    }
}