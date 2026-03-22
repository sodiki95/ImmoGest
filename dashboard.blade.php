@extends('back.app')

@section('title', 'Dashboard - Gestion Locative')

@section('dashboard-header')
    <div class="row">
        <div class="col-sm-12 mt-5">
            <h3 class="page-title mt-3">Bonjour, {{ \Illuminate\Support\Facades\Auth::user()->name }} !</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ul>
        </div>
    </div>
@endsection

@section('dashboard-content')

    {{-- ===== LIGNE 1 : KPI principaux ===== --}}
    <div class="row">

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $totalBiens }}</h3>
                            <h6 class="text-muted">Biens gérés</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ number_format($loyersEncaisses, 0, ',', ' ') }} F</h3>
                            <h6 class="text-muted">Loyers encaissés</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $totalImpayes }}</h3>
                            <h6 class="text-muted">Impayés en cours</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $tauxOccupation }} %</h3>
                            <h6 class="text-muted">Taux d'occupation</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== LIGNE 2 : KPI secondaires ===== --}}
    <div class="row">

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $totalLocataires }}</h3>
                            <h6 class="text-muted">Locataires actifs</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $totalContrats }}</h3>
                            <h6 class="text-muted">Contrats actifs</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $totalQuittances }}</h3>
                            <h6 class="text-muted">Quittances émises</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="12" y1="18" x2="12" y2="12"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card board1 fill">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <div>
                            <h3 class="card_widget_header">{{ $bauxExpirants }}</h3>
                            <h6 class="text-muted">Baux expirant (30j)</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009688" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== IMPAYÉS + DERNIERS PAIEMENTS ===== --}}
    <div class="row">

        {{-- Impayés en cours --}}
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title float-left mt-2">Impayés en cours</h4>
                    <a href="{{ route('rent-calls.index') }}" class="btn btn-primary float-right veiwbutton">
                        Voir tous
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center">
                            <thead>
                                <tr>
                                    <th>Locataire</th>
                                    <th>Bien</th>
                                    <th class="text-right">Montant</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($impayes as $impaye)
                                    <tr>
                                        <td class="text-nowrap">
                                            {{ optional($impaye->contract->tenant)->prenom }}
                                            {{ optional($impaye->contract->tenant)->nom }}
                                        </td>
                                        <td>{{ optional($impaye->contract->property)->adresse ?? optional($impaye->contract->property)->titre ?? '—' }}</td>
                                        <td class="text-right text-nowrap">
                                            {{ number_format($impaye->montant ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill bg-danger inv-badge">En cours</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun impayé en cours</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Derniers paiements reçus --}}
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title float-left mt-2">Derniers paiements</h4>
                    <a href="{{ route('payments.index') }}" class="btn btn-primary float-right veiwbutton">
                        Voir tous
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center">
                            <thead>
                                <tr>
                                    <th>Locataire</th>
                                    <th>Bien</th>
                                    <th class="text-right">Montant</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($derniersPaiements as $paiement)
                                    <tr>
                                        <td class="text-nowrap">
                                            {{ optional($paiement->tenant)->prenom }}
                                            {{ optional($paiement->tenant)->nom }}
                                        </td>
                                        <td>{{ optional($paiement->contract->property)->adresse ?? optional($paiement->contract->property)->titre ?? '—' }}</td>
                                        <td class="text-right text-nowrap">
                                            {{ number_format($paiement->montant_paye ?? 0, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill bg-success inv-badge">Payé</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun paiement enregistré</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== CONTRATS RÉCENTS + ALERTES ===== --}}
    <div class="row">

        {{-- Contrats récents --}}
        <div class="col-md-7 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title float-left mt-2">Contrats récents</h4>
                    <a href="{{ route('contracts.index') }}" class="btn btn-primary float-right veiwbutton">
                        Voir tous
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center">
                            <thead>
                                <tr>
                                    <th>Réf.</th>
                                    <th>Locataire</th>
                                    <th>Bien</th>
                                    <th class="text-right">Loyer (F)</th>
                                    <th class="text-center">Début</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contratsRecents as $contrat)
                                    <tr>
                                        <td class="text-nowrap">
                                            CONT-{{ str_pad($contrat->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="text-nowrap">
                                            {{ optional($contrat->tenant)->prenom }}
                                            {{ optional($contrat->tenant)->nom }}
                                        </td>
                                        <td>{{ optional($contrat->property)->adresse ?? optional($contrat->property)->titre ?? '—' }}</td>
                                        <td class="text-right text-nowrap">
                                            {{ number_format($contrat->loyer_mensuel ?? $contrat->loyer ?? 0, 0, ',', ' ') }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                                        </td>
                                        <td class="text-center">
                                            @if($contrat->statut === 'actif')
                                                <span class="badge badge-pill bg-success inv-badge">ACTIF</span>
                                            @elseif(in_array($contrat->statut, ['termine', 'resilie']))
                                                <span class="badge badge-pill bg-danger inv-badge">{{ strtoupper($contrat->statut) }}</span>
                                            @else
                                                <span class="badge badge-pill bg-warning inv-badge">EN ATTENTE</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Aucun contrat enregistré</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertes & échéances --}}
        <div class="col-md-5 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title float-left mt-2">Alertes & échéances</h4>
                    <a href="{{ route('rent-calls.index') }}" class="btn btn-primary float-right veiwbutton">
                        Voir toutes
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Bien / Locataire</th>
                                    <th class="text-center">Échéance</th>
                                    <th class="text-center">Urgence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alertes as $alerte)
                                    <tr>
                                        <td class="text-nowrap">{{ $alerte['type'] }}</td>
                                        <td>{{ $alerte['label'] }}</td>
                                        <td class="text-center text-nowrap">
                                            {{ \Carbon\Carbon::parse($alerte['date'])->format('d/m/Y') }}
                                        </td>
                                        <td class="text-center">
                                            @if($alerte['urgence'] === 'haute')
                                                <span class="badge badge-pill bg-danger inv-badge">URGENT</span>
                                            @elseif($alerte['urgence'] === 'moyenne')
                                                <span class="badge badge-pill bg-warning inv-badge">BIENTÔT</span>
                                            @else
                                                <span class="badge badge-pill bg-info inv-badge">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucune alerte</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
