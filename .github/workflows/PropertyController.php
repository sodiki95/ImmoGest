<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('owner'); //  chargement du propriétaire

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('titre', 'like', "%{$request->search}%")
                  ->orWhere('ville', 'like', "%{$request->search}%")
                  ->orWhere('adresse', 'like', "%{$request->search}%");
            });
        }

        $properties = $query->latest()->paginate(9);
        $owners     = Owner::orderBy('nom')->get(); // pour le select dans les modales

        return view('back.Proprietes.index', compact('properties', 'owners'));
    }

    public function create()
    {
        return view('back.Proprietes.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'type'        => 'required|in:appartement,maison,villa,studio,bureau,terrain',
            'statut'      => 'required|in:disponible,loue,en_vente,vendu',
            'description' => 'nullable|string',
            'adresse'     => 'required|string|max:255',
            'ville'       => 'required|string|max:100',
            'code_postal' => 'required|string|max:10',
            'superficie'  => 'required|numeric|min:1',
            'nb_pieces'   => 'nullable|integer|min:1',
            'nb_chambres' => 'nullable|integer|min:0',
            'prix'        => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8048',
            'owner_id'    => 'nullable|exists:owners,id', // ajout
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = $request->file('image')->store('properties', 'public');
        }

        Property::create($validated);

        return redirect()->route('properties.index')->with('success', 'Bien immobilier ajouté avec succès.');
    }

    public function show(Property $property)
    {
        $property->load('owner');
        $owners = Owner::orderBy('nom')->get();
        return view('back.Proprietes.show', compact('property', 'owners'));
    }

    public function edit(Property $property)
    {
        return view('back.Proprietes.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'type'        => 'required|in:appartement,maison,villa,studio,bureau,terrain',
            'statut'      => 'required|in:disponible,loue,en_vente,vendu',
            'description' => 'nullable|string',
            'adresse'     => 'required|string|max:255',
            'ville'       => 'required|string|max:100',
            'code_postal' => 'required|string|max:10',
            'superficie'  => 'required|numeric|min:1',
            'nb_pieces'   => 'nullable|integer|min:1',
            'nb_chambres' => 'nullable|integer|min:0',
            'prix'        => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8048',
            'owner_id'    => 'nullable|exists:owners,id', // ajout
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $validated['image'] = $request->file('image')->store('properties', 'public');
        } else {
            unset($validated['image']); // garde l'ancienne image si pas de nouvelle
        }

        $property->update($validated);

        return redirect()->route('properties.index')->with('success', 'Bien immobilier mis à jour avec succès.');
    }

    public function destroy(Property $property)
    {
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }
        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Bien immobilier supprimé avec succès.');
    }
}
