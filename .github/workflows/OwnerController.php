<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Owner::withCount('properties');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%")
                  ->orWhere('telephone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $owners = $query->latest()->paginate(10);

        return view('back.owners.index', compact('owners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'email'      => 'nullable|email|unique:owners,email',
            'telephone'  => 'required|string|max:20',
            'telephone2' => 'nullable|string|max:20',
            'type'       => 'required|in:particulier,entreprise',
            'entreprise' => 'nullable|string|max:255',
            'adresse'    => 'nullable|string|max:255',
            'ville'      => 'nullable|string|max:100',
            'code_postal'=> 'nullable|string|max:10',
            'cni'        => 'nullable|string|max:50',
            'notes'      => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $validated['photo'] = $request->file('photo')->store('owners', 'public');
        }

        Owner::create($validated);
        //dd($request->all(), $request->hasFile('photo')); // Debug pour vérifier la présence du fichier

        return redirect()->route('owners.index')
                         ->with('success', 'Propriétaire ajouté avec succès.');
    }

    public function show(Owner $owner)
    {
        $owner->loadCount('properties');
        $owner->load('properties');
        return view('back.owners.show', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'email'      => 'nullable|email|unique:owners,email,' . $owner->id,
            'telephone'  => 'required|string|max:20',
            'telephone2' => 'nullable|string|max:20',
            'type'       => 'required|in:particulier,entreprise',
            'entreprise' => 'nullable|string|max:255',
            'adresse'    => 'nullable|string|max:255',
            'ville'      => 'nullable|string|max:100',
            'code_postal'=> 'nullable|string|max:10',
            'cni'        => 'nullable|string|max:50',
            'notes'      => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5048',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($owner->photo) {
                Storage::disk('public')->delete($owner->photo);
            }
            $validated['photo'] = $request->file('photo')->store('owners', 'public');
        } else {
            unset($validated['photo']);
        }

        $owner->update($validated);

        return redirect()->route('owners.index')
                         ->with('success', 'Propriétaire mis à jour avec succès.');
    }

    public function destroy(Owner $owner)
    {
        if ($owner->photo) {
            Storage::disk('public')->delete($owner->photo);
        }
        $owner->delete();

        return redirect()->route('owners.index')
                         ->with('success', 'Propriétaire supprimé avec succès.');
    }
}
