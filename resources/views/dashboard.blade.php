@extends('layout')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-fadein" style="animation-delay: 0.02s">
    <h1 class="h3 mb-0">Dashboard</h1>
</div>

{{-- Sélecteur de période --}}
<div class="animate-fadein" style="animation-delay: 0.05s">
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
</div>

{{-- 4 KPI tiles --}}
<div class="row g-3 mb-4 animate-fadein" style="animation-delay: 0.1s">
    <div class="col-6 col-md-3">
        <div class="card card-balance h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-wallet2"></i></span>
                <div class="text-muted small mb-1">Solde total</div>
                <div class="h5 mb-0 amount-income" id="total-balance">…</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-arrow-left-right"></i></span>
                <div class="text-muted small mb-1">Transactions</div>
                <div class="h5 mb-0" id="kpi-tx-count">…</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-calendar-day"></i></span>
                <div class="text-muted small mb-1">Dépense/jour moy.</div>
                <div class="h5 mb-0 amount-expense" id="kpi-daily-expense">…</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-trophy"></i></span>
                <div class="text-muted small mb-1">Top dépense</div>
                <div class="h5 mb-0 text-truncate" id="kpi-top-category" title="">…</div>
            </div>
        </div>
    </div>
</div>

{{-- Cartes comptes avec mini barre % --}}
<div class="row mb-4 animate-fadein" style="animation-delay: 0.15s">
    <div class="col-12">
        <div class="row g-2" id="account-cards"></div>
    </div>
</div>

{{-- Résumé période --}}
<div class="row mb-4 animate-fadein" style="animation-delay: 0.18s">
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Total entrées</div>
                <div class="h5 amount-income" id="period-income">…</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Total sorties</div>
                <div class="h5 amount-expense" id="period-expense">…</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small">Solde net</div>
                <div class="h5" id="period-net">…</div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques --}}
<div class="row mb-4 animate-fadein" style="animation-delay: 0.22s">
    <div class="col-12 col-md-5 mb-3">
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
    <div class="col-12 col-md-7 mb-3">
        <div class="card h-100">
            <div class="card-header">Revenus vs Dépenses (12 mois)</div>
            <div class="card-body">
                <canvas id="chart-monthly-bar"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- 10 dernières transactions --}}
<div class="card animate-fadein" style="animation-delay: 0.27s">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>10 dernières transactions</span>
        <a href="/transactions" class="btn btn-sm btn-outline-primary">
            Voir toutes <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div id="no-recent-tx" class="text-center text-muted py-4 d-none">
            Aucune transaction enregistrée
        </div>
        <table class="table table-hover align-middle mb-0" id="recent-tx-table">
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
<script src="{{ assetVersion('js/dashboard.js') }}"></script>
@endpush
