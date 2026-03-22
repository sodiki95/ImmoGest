<?php

namespace App\Http\Controllers;

use App\Models\RentCall;
use App\Models\Contract;
use Illuminate\Http\Request;

class RentCallController extends Controller
{
    public function index(Request $request)
    {
        $query = RentCall::with(['contract.tenant', 'contract.property']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->whereHas('contract.tenant', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%");
            });
        }

        // Mettre en retard les appels non payés dépassés
        RentCall::where('statut', 'en_attente')
                ->where('date_echeance', '<', now())
                ->update(['statut' => 'en_retard']);

        $rentCalls = $query->latest()->paginate(10);
        $contracts = Contract::with(['tenant', 'property'])
                             ->where('statut', 'actif')
                             ->get();

        return view('back.Quitance.rent_calls', compact('rentCalls', 'contracts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id'   => 'required|exists:contracts,id',
            'periode'       => 'required|date',
            'montant' => 'required|numeric|min:0',
            'date_echeance' => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $validated['statut'] = 'en_attente';
        $validated['montant_paye'] = 0;
        $validated['reference'] = 'RC-' . date('Ym') . '-' . str_pad(RentCall::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $validated['date_appel'] = now();
        RentCall::create($validated);

        return redirect()->route('rent-calls.index')->with('success', 'Appel de loyer créé avec succès.');
    }

    public function show(RentCall $rentCall)
    {
        $rentCall->load(['contract.tenant', 'contract.property', 'receipts']);
        return view('back.Quitance.rent_call_show', compact('rentCall'));
    }

    public function update(Request $request, RentCall $rentCall)
    {
        $validated = $request->validate([
            'contract_id'   => 'required|exists:contracts,id',
            'periode'       => 'required|date',
            'montant' => 'required|numeric|min:0',
            'date_echeance' => 'required|date',
            'statut'        => 'required|in:en_attente,paye,partiel,en_retard',
            'notes'         => 'nullable|string',
        ]);
        $validated['reference'] = 'RC-' . date('Ym') . '-' . str_pad(RentCall::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $rentCall->update($validated);

        return redirect()->route('rent-calls.index')->with('success', 'Appel de loyer mis à jour.');
    }

    public function destroy(RentCall $rentCall)
    {
        $rentCall->delete();
        return redirect()->route('rent-calls.index')->with('success', 'Appel de loyer supprimé.');
    }
}
