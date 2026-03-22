<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Mettre à jour automatiquement les statuts en retard
        Payment::where('statut', 'en_attente')
               ->where('date_echeance', '<', now())
               ->update(['statut' => 'en_retard']);

        $query = Payment::with(['tenant', 'property', 'contract']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('tenant', fn($t) =>
                      $t->where('nom', 'like', "%{$request->search}%")
                        ->orWhere('prenom', 'like', "%{$request->search}%")
                  );
            });
        }
        if ($request->filled('periode')) {
            $query->whereYear('periode', substr($request->periode, 0, 4))
                  ->whereMonth('periode', substr($request->periode, 5, 2));
        }

        $payments  = $query->latest('date_echeance')->paginate(15);
        $contracts = Contract::with(['tenant', 'property'])
                             ->where('statut', 'actif')->get();

        // Stats globales
        $stats = [
            'total_du'     => Payment::whereNotIn('statut', ['paye'])->sum('montant_du'),
            'total_paye'   => Payment::sum('montant_paye'),
            'total_impaye' => Payment::whereIn('statut', ['impaye', 'en_retard'])->sum(\DB::raw('montant_du - montant_paye')),
            'nb_en_retard' => Payment::whereIn('statut', ['en_retard', 'impaye'])->count(),
        ];

        return view('back.Payment.index', compact('payments', 'contracts', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id'       => 'required|exists:contracts,id',
            'periode'           => 'required|date',
            'date_echeance'     => 'required|date',
            'montant_du'        => 'required|numeric|min:1',
            'notes'             => 'nullable|string',
        ]);

        $contract = Contract::with(['tenant', 'property'])->findOrFail($validated['contract_id']);

        $validated['tenant_id']   = $contract->tenant_id;
        $validated['property_id'] = $contract->property_id;
        $validated['reference']   = Payment::generateReference();
        $validated['statut']      = 'en_attente';
        $validated['montant_paye']= 0;

        Payment::create($validated);

        return redirect()->route('payments.index')
                         ->with('success', "Paiement {$validated['reference']} créé avec succès.");
    }

    public function show(Payment $payment)
    {
        $payment->load(['tenant', 'property.owner', 'contract']);
        return view('back.Payment.show', compact('payment'));
    }

    // Enregistrer un paiement sur une échéance
    public function pay(Request $request, Payment $payment)
    {
        $request->validate([
            'montant_paye'      => 'required|numeric|min:1',
            'date_paiement'     => 'required|date',
            'mode_paiement'     => 'required|in:especes,virement,cheque,mobile_money',
            'numero_transaction'=> 'nullable|string|max:100',
            'penalite'          => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $payment->update([
            'montant_paye'       => $payment->montant_paye + $request->montant_paye,
            'date_paiement'      => $request->date_paiement,
            'mode_paiement'      => $request->mode_paiement,
            'numero_transaction' => $request->numero_transaction,
            'penalite'           => $request->penalite ?? 0,
            'notes'              => $request->notes,
        ]);

        $payment->refresh()->updateStatut();

        return redirect()->route('payments.index')
                         ->with('success', 'Paiement enregistré avec succès.');
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'periode'       => 'required|date',
            'date_echeance' => 'required|date',
            'montant_du'    => 'required|numeric|min:1',
            'statut'        => 'required|in:en_attente,paye,partiel,impaye,en_retard',
            'notes'         => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')
                         ->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')
                         ->with('success', 'Paiement supprimé.');
    }

    // Générer automatiquement les échéances du mois
    public function genererEcheances()
    {
        $contracts = Contract::with(['tenant', 'property'])
                             ->where('statut', 'actif')->get();

        $created = 0;
        foreach ($contracts as $contract) {
            $periode = now()->startOfMonth();
            $exists  = Payment::where('contract_id', $contract->id)
                               ->whereYear('periode', $periode->year)
                               ->whereMonth('periode', $periode->month)
                               ->exists();

            if (!$exists) {
                Payment::create([
                    'contract_id'  => $contract->id,
                    'tenant_id'    => $contract->tenant_id,
                    'property_id'  => $contract->property_id,
                    'reference'    => Payment::generateReference(),
                    'periode'      => $periode,
                    'date_echeance'=> $periode->copy()->day($contract->jour_paiement),
                    'montant_du'   => $contract->loyer_mensuel,
                    'montant_paye' => 0,
                    'statut'       => 'en_attente',
                ]);
                $created++;
            }
        }

        return redirect()->route('payments.index')
                         ->with('success', "{$created} échéance(s) générée(s) pour " . now()->format('m/Y') . ".");
    }
}
