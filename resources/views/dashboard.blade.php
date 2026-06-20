@extends('layout')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
</div>

{{-- Sélecteur de période --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary btn-sm" data-period="day">Aujourd'hui</button>
                <button class="btn btn-outline-primary btn-sm" data-period="week">Semaine</button>
                <button class="btn btn-outline-primary btn-sm active" data-period="month">Mois</button>
                <button class="btn btn-outline-primary btn-sm" data-period="year">Année</button>
            </div>
            <div class="d-flex gap-2 ms-2">
                <input type="date" id="start-date" class="form-control form-control-sm" style="width:160px">
                <input type="date" id="end-date" class="form-control form-control-sm" style="width:160px">
                <button class="btn btn-primary btn-sm" id="apply-period">Appliquer</button>
            </div>
        </div>
    </div>
</div>

{{-- Solde total + par compte --}}
<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3">
        <div class="card card-balance h-100">
            <div class="card-body">
                <div class="text-muted small">Solde total</div>
                <div class="h4 mb-0 amount-income" id="total-balance">—</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="row g-2" id="account-cards"></div>
    </div>
</div>

{{-- Résumé période --}}
<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Total entrées</div>
                <div class="h5 amount-income" id="period-income">—</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Total sorties</div>
                <div class="h5 amount-expense" id="period-expense">—</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Solde net</div>
                <div class="h5" id="period-net">—</div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques --}}
<div class="row mb-4">
    <div class="col-12 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Dépenses par catégorie</div>
            <div class="card-body">
                <div id="no-expense-data" class="text-center text-muted py-4 d-none">
                    Aucune dépense sur cette période
                </div>
                <canvas id="chart-expense-by-category"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Évolution du solde</div>
            <div class="card-body">
                <canvas id="chart-balance-evolution"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- 10 dernières transactions --}}
<div class="card">
    <div class="card-header">10 dernières transactions</div>
    <div class="card-body p-0">
        <div id="no-recent-tx" class="text-center text-muted py-4 d-none">
            Aucune transaction enregistrée
        </div>
        <table class="table table-hover mb-0" id="recent-tx-table">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Catégorie</th>
                    <th>Compte</th>
                    <th class="text-end">Montant</th>
                </tr>
            </thead>
            <tbody id="recent-tx-body"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/dashboard.js"></script>
@endpush
