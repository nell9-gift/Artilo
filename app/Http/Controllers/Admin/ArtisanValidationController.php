<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtisanValidationController extends Controller
{
    // Affiche la liste des artisans en attente de validation
    public function index()
    {
        $artisans = Artisan::with('user')
            ->where('status', 'pending')   // ← colonne "status" et valeur "pending"
            ->latest()
            ->paginate(20);

        return view('admin.artisans.index', compact('artisans'));
    }

    // Valider un artisan (le faire passer en "approved")
    public function valider(Artisan $artisan)
    {
        $artisan->update([
            'status' => 'approved',   // ← colonne "status" et valeur "approved"
        ]);

        // TODO: envoyer un email ou SMS de validation

        return back()->with('success', "Artisan {$artisan->user->name} validé avec succès !");
    }

    // Refuser un artisan (le faire passer en "rejected" avec un motif)
    public function refuser(Request $request, Artisan $artisan)
    {
        $request->validate([
            'refusal_reason' => 'required|string|max:500',   // ← colonne "refusal_reason"
        ]);

        $artisan->update([
            'status' => 'rejected',                        // ← colonne "status" et valeur "rejected"
            'refusal_reason' => $request->refusal_reason,  // ← colonne "refusal_reason"
        ]);

        // TODO: envoyer un email ou SMS de refus

        return back()->with('success', "Artisan {$artisan->user->name} refusé.");
    }

    // (Optionnel) Afficher le document d'identité d'un artisan
    public function voirDocument(Artisan $artisan)
    {
        if (! Storage::disk('private')->exists($artisan->identity_document)) {
            abort(404, 'Document introuvable.');
        }

        return Storage::disk('private')->response($artisan->identity_document);
    }
}
