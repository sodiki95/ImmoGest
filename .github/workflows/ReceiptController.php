<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\RentCall;
use App\Models\Contract;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with(['contract.tenant', 'contract.property', 'rentCall']);

        if ($request->filled('search')) {
            $query->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('contract.tenant', function ($q) use ($request) {
                      $q->where('nom', 'like', "%{$request->search}%")
                        ->orWhere('prenom', 'like', "%{$request->search}%");
                  });
        }

        $receipts  = $query->latest()->paginate(10);
        $rentCalls = RentCall::with(['contract.tenant', 'contract.property'])
                             ->whereIn('statut', ['en_attente', 'partiel', 'en_retard'])
                             ->get();

        return view('back.Quitance.receipts', compact('receipts', 'rentCalls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rent_call_id'      => 'required|exists:rent_calls,id',
            'date_paiement'     => 'required|date',
            'montant'           => 'required|numeric|min:1',
            'mode_paiement'     => 'required|in:especes,virement,cheque,mobile_money',
            'numero_transaction'=> 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        $rentCall = RentCall::findOrFail($validated['rent_call_id']);

        $validated['contract_id'] = $rentCall->contract_id;
        $validated['reference']   = Receipt::generateReference();

        Receipt::create($validated);

        // Mettre à jour le montant payé et le statut de l'appel
        $rentCall->increment('montant_paye', $validated['montant']);
        $rentCall->refresh()->updateStatut();

        return redirect()->route('receipts.index')
                         ->with('success', "Quittance {$validated['reference']} créée avec succès.");
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['contract.tenant', 'contract.property.owner', 'rentCall']);
        return view('back.Quitance.receipt_show', compact('receipt'));
    }

    public function destroy(Receipt $receipt)
    {
        // Déduire le montant de l'appel de loyer
        $rentCall = $receipt->rentCall;
        $rentCall->decrement('montant_paye', $receipt->montant);
        $rentCall->refresh()->updateStatut();

        $receipt->delete();

        return redirect()->route('receipts.index')
                         ->with('success', 'Quittance supprimée.');
    }
}
