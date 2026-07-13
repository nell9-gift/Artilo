<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MetierController extends Controller
{
    public function index()
    {
        $metiers = Metier::withCount('artisans')->orderBy('nom')->get();
        return view('admin.metiers.index', compact('metiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($data['nom']);

        Metier::create($data);

        return back()->with('success', 'Métier ajouté.');
    }

    public function update(Request $request, Metier $metier)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'description' => 'nullable|string',
            'actif'       => 'boolean',
        ]);
        $metier->update($data);

        return back()->with('success', 'Métier mis à jour.');
    }

    public function destroy(Metier $metier)
    {
        abort_if($metier->artisans()->exists(), 422, 'Impossible : des prestataires utilisent ce métier.');
        $metier->delete();

        return back()->with('success', 'Métier supprimé.');
    }
}