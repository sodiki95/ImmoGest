<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Property;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::with(['property', 'owner']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', "%{$request->search}%")
                  ->orWhere('fournisseur', 'like', "%{$request->search}%")
                  ->orWhereHas('property', fn($p) =>
                      $p->where('titre', 'like', "%{$request->search}%")
                  );
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $charges    = $query->latest('date_charge')->paginate(15);
        $properties = Property::orderBy('titre')->get();
        $owners     = Owner::orderBy('nom')->get();

        // Stats
        $stats = [
            'total'          => Charge::sum('montant'),
            'total_paye'     => Charge::where('statut', 'paye')->sum('montant'),
            'total_en_attente' => Charge::where('statut', 'en_attente')->sum('montant'),
            'nb_en_attente'  => Charge::where('statut', 'en_attente')->count(),
        ];

        return view('back.Charges.index', compact('charges', 'properties', 'owners', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id'    => 'required|exists:properties,id',
            'owner_id'       => 'nullable|exists:owners,id',
            'titre'          => 'required|string|max:255',
            'categorie'      => 'required|in:entretien,reparation,taxe,assurance,syndic,eau,electricite,internet,gardiennage,autres',
            'statut'         => 'required|in:en_attente,paye,annule',
            'periodicite'    => 'required|in:unique,mensuel,trimestriel,annuel',
            'montant'        => 'required|numeric|min:0',
            'date_charge'    => 'required|date',
            'date_paiement'  => 'nullable|date',
            'fournisseur'    => 'nullable|string|max:255',
            'numero_facture' => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'document'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            $validated['document'] = $request->file('document')->store('charges', 'public');
        }

        Charge::create($validated);

        return redirect()->route('charges.index')
                         ->with('success', 'Charge ajoutée avec succès.');
    }

    public function show(Charge $charge)
    {
        $charge->load(['property.owner', 'owner']);
        $properties = Property::orderBy('titre')->get();
        $owners     = Owner::orderBy('nom')->get();
        return view('back.Charges.show', compact('charge', 'properties','owners'));
    }

    public function update(Request $request, Charge $charge)
    {
        $validated = $request->validate([
            'property_id'    => 'required|exists:properties,id',
            'owner_id'       => 'nullable|exists:owners,id',
            'titre'          => 'required|string|max:255',
            'categorie'      => 'required|in:entretien,reparation,taxe,assurance,syndic,eau,electricite,internet,gardiennage,autre',
            'statut'         => 'required|in:en_attente,paye,annule',
            'periodicite'    => 'required|in:unique,mensuel,trimestriel,annuel',
            'montant'        => 'required|numeric|min:0',
            'date_charge'    => 'required|date',
            'date_paiement'  => 'nullable|date',
            'fournisseur'    => 'nullable|string|max:255',
            'numero_facture' => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'document'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            if ($charge->document) {
                Storage::disk('public')->delete($charge->document);
            }
            $validated['document'] = $request->file('document')->store('charges', 'public');
        } else {
            unset($validated['document']);
        }

        $charge->update($validated);

        return redirect()->route('charges.index')
                         ->with('success', 'Charge mise à jour avec succès.');
    }

    public function destroy(Charge $charge)
    {
        if ($charge->document) {
            Storage::disk('public')->delete($charge->document);
        }
        $charge->delete();

        return redirect()->route('charges.index')
                         ->with('success', 'Charge supprimée avec succès.');
    }

    // Marquer comme payée rapidement
    public function payer(Charge $charge)
    {
        $charge->update([
            'statut'        => 'paye',
            'date_paiement' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Charge marquée comme payée.');
    }
}
