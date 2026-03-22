<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\RentCall;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPI ───────────────────────────────────────────────────────────────
        $totalBiens      = Property::count();

        $totalLocataires = Tenant::whereHas('contracts', fn($q) => $q->where('statut', 'actif'))->count();

        $totalContrats   = Contract::where('statut', 'actif')->count();

        $totalQuittances = RentCall::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count();

        $loyersEncaisses = Payment::whereMonth('date_paiement', now()->month)
                                  ->whereYear('date_paiement', now()->year)
                                  ->sum('montant_paye');

        $totalImpayes    = RentCall::where('statut', 'en_cours')->count();

        $biensLoues      = Contract::where('statut', 'actif')
                                   ->distinct('property_id')
                                   ->count('property_id');

        $tauxOccupation  = $totalBiens > 0 ? round(($biensLoues / $totalBiens) * 100, 1) : 0;

        $bauxExpirants   = Contract::where('statut', 'actif')
                                   ->whereBetween('date_fin', [now(), now()->addDays(30)])
                                   ->count();

        // ── Tableaux ──────────────────────────────────────────────────────────
        // RentCall : impayés en cours avec relations tenant et property
        $impayes = RentCall::with(['tenant', 'contract.property'])
                           ->where('statut', 'en_cours')
                           ->orderByDesc('created_at')
                           ->limit(5)
                           ->get();

        // Payment :paiement récents avec relations tenant et property
        $derniersPaiements = Payment::with(['tenant', 'contract.property'])
                                    ->orderByDesc('date_paiement')
                                    ->limit(5)
                                    ->get();

        // Contract : contrats récents avec relations tenant et property
        $contratsRecents = Contract::with(['tenant', 'property'])
                                   ->orderByDesc('created_at')
                                   ->limit(6)
                                   ->get();

        // ── Alertes : baux expirant dans 30 jours ─────────────────────────────
        $alertes = [];
        // Récupérer les contrats actifs expirant dans les 30 prochains jours
        Contract::with(['tenant', 'property'])
            ->where('statut', 'actif')
            ->whereBetween('date_fin', [now(), now()->addDays(30)])
            ->get()
            ->each(function ($bail) use (&$alertes) {
                $jours = (int) now()->diffInDays($bail->date_fin);
                $alertes[] = [
                    'type'    => 'Bail expirant',
                    'label'   => optional($bail->tenant)->nom . ' ' . optional($bail->tenant)->prenom,
                    'date'    => $bail->date_fin,
                    'urgence' => $jours <= 7 ? 'haute' : ($jours <= 15 ? 'moyenne' : 'basse'),
                ];
            });

        // Trier par date d'échéance croissante
        usort($alertes, fn($a, $b) => strtotime($a['date']) - strtotime($b['date']));

        return view('back.dashboard', compact(
            'totalBiens',
            'totalLocataires',
            'totalContrats',
            'totalQuittances',
            'loyersEncaisses',
            'totalImpayes',
            'tauxOccupation',
            'bauxExpirants',
            'impayes',
            'derniersPaiements',
            'contratsRecents',
            'alertes',
        ));
    }
}
