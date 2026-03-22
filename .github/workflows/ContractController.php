<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with(['tenant', 'property']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->whereHas('tenant', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%");
            })->orWhereHas('property', function ($q) use ($request) {
                $q->where('titre', 'like', "%{$request->search}%");
            });
        }

        $contracts  = $query->latest()->paginate(10);
        $tenants    = Tenant::orderBy('nom')->cursor();
        $properties = Property::orderBy('titre')->cursor();
        //$properties = Property::where('statut', 'disponible')
                              // ->orWhereHas('activeContract')
                               //->orderBy('titre')->get();

        return view('back.Contracts.index', compact('contracts', 'tenants', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'     => 'required|exists:tenants,id',
            'property_id'   => 'required|exists:properties,id',
            'type'          => 'required|in:location,vente',
            'statut'        => 'required|in:actif,termine,resilie,en_attente',
            'date_debut'    => 'required|date',
            'date_fin'      => 'nullable|date|after:date_debut',
            'loyer_mensuel' => 'required|numeric|min:0',
            'caution'       => 'nullable|numeric|min:0',
            'jour_paiement' => 'nullable|integer|min:1|max:31',
            'periodicite'   => 'required|in:mensuel,trimestriel,annuel',
            'conditions'    => 'nullable|string',
            'document'      => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $validated['document'] = $request->file('document')->store('contracts', 'public');
        }

        // Mettre le bien en "loué" si contrat actif
        if ($validated['statut'] === 'actif') {
            Property::where('id', $validated['property_id'])
                    ->update(['statut' => 'loue']);
        }

        Contract::create($validated);

        return redirect()->route('contracts.index')->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['tenant', 'property.owner']);
        $tenants    = Tenant::orderBy('nom')->cursor();
        $properties = Property::orderBy('titre')->cursor();
        return view('back.Contracts.show', compact('contract', 'tenants', 'properties'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'tenant_id'     => 'required|exists:tenants,id',
            'property_id'   => 'required|exists:properties,id',
            'type'          => 'required|in:location,vente',
            'statut'        => 'required|in:actif,termine,resilie,en_attente',
            'date_debut'    => 'required|date',
            'date_fin'      => 'nullable|date|after:date_debut',
            'loyer_mensuel' => 'required|numeric|min:0',
            'caution'       => 'nullable|numeric|min:0',
            'jour_paiement' => 'nullable|integer|min:1|max:31',
            'periodicite'   => 'required|in:mensuel,trimestriel,annuel',
            'conditions'    => 'nullable|string',
            'document'      => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            if ($contract->document) {
                Storage::disk('public')->delete($contract->document);
            }
            $validated['document'] = $request->file('document')->store('contracts', 'public');
        } else {
            unset($validated['document']);
        }

        // Mettre à jour le statut du bien
        if ($validated['statut'] === 'actif') {
            Property::where('id', $validated['property_id'])->update(['statut' => 'loue']);
        } elseif (in_array($validated['statut'], ['termine', 'resilie'])) {
            Property::where('id', $validated['property_id'])->update(['statut' => 'disponible']);
        }

        $contract->update($validated);

        return redirect()->route('contracts.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->document) {
            Storage::disk('public')->delete($contract->document);
        }
        // Remettre le bien disponible
        $contract->property()->update(['statut' => 'disponible']);
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contrat supprimé avec succès.');
    }
}
