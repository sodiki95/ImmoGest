<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::withCount('contracts');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%")
                  ->orWhere('telephone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $tenants = $query->latest()->paginate(10);

        return view('back.tenants.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'email'      => 'nullable|email|unique:tenants,email',
            'telephone'  => 'required|string|max:10',
            'telephone2' => 'nullable|string|max:10',
            'adresse'    => 'nullable|string|max:255',
            'ville'      => 'nullable|string|max:100',
            'code_postal'=> 'nullable|string|max:10',
            'cni'        => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
            'employeur'  => 'nullable|string|max:100',
            'notes'      => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4048',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        Tenant::create($validated);

        return redirect()->route('tenants.index')
                         ->with('success', 'Locataire ajouté avec succès.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['contracts.property']);
        $tenant->loadCount('contracts');
        return view('back.Tenants.show', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'email'      => 'nullable|email|unique:tenants,email,' . $tenant->id,
            'telephone'  => 'required|string|max:10',
            'telephone2' => 'nullable|string|max:10',
            'adresse'    => 'nullable|string|max:255',
            'ville'      => 'nullable|string|max:100',
            'code_postal'=> 'nullable|string|max:10',
            'cni'        => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
            'employeur'  => 'nullable|string|max:100',
            'notes'      => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4048',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        } else {
            unset($validated['photo']);
        }

        $tenant->update($validated);

        return redirect()->route('tenants.index')
                         ->with('success', 'Locataire mis à jour avec succès.');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->photo) {
            Storage::disk('public')->delete($tenant->photo);
        }
        $tenant->delete();

        return redirect()->route('tenants.index')
                         ->with('success', 'Locataire supprimé avec succès.');
    }
}
