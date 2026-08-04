@extends('layout')

@section('title', 'Administration')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-fadein" style="animation-delay: 0.02s">
    <h1 class="h3 mb-0"><i class="bi bi-speedometer2"></i> Administration — Vue globale</h1>
</div>

{{-- Sélecteur de période --}}
<div class="animate-fadein" style="animation-delay: 0.05s">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-primary btn-sm" data-period="week">7 jours</button>
                    <button class="btn btn-outline-primary btn-sm active" data-period="month">30 jours</button>
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

{{-- KPI tiles --}}
<div class="row g-3 mb-4 animate-fadein" style="animation-delay: 0.1s">
    <div class="col-6 col-md-3 col-xl">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-people"></i></span>
                <div class="text-muted small mb-1">Inscrits (total)</div>
                <div class="h5 mb-0" id="kpi-total-users">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-person-plus"></i></span>
                <div class="text-muted small mb-1">Nouveaux (période)</div>
                <div class="h5 mb-0" id="kpi-new-users">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-activity"></i></span>
                <div class="text-muted small mb-1">Actifs (30 j)</div>
                <div class="h5 mb-0" id="kpi-active-users">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-arrow-left-right"></i></span>
                <div class="text-muted small mb-1">Opérations (période)</div>
                <div class="h5 mb-0" id="kpi-operations">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card h-100">
            <div class="card-body">
                <span class="kpi-icon"><i class="bi bi-shield-check"></i></span>
                <div class="text-muted small mb-1">Taux de consentement</div>
                <div class="h5 mb-0" id="kpi-consent-rate">—</div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques usage --}}
<div class="row mb-4 animate-fadein" style="animation-delay: 0.15s">
    <div class="col-12 col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Inscriptions — 12 mois</div>
            <div class="card-body"><canvas id="chart-user-growth"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Visites par jour</div>
            <div class="card-body"><canvas id="chart-traffic"></canvas></div>
        </div>
    </div>
</div>

<div class="row mb-4 animate-fadein" style="animation-delay: 0.18s">
    <div class="col-12 col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Utilisateurs actifs par jour</div>
            <div class="card-body"><canvas id="chart-active-users"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Opérations par type (période)</div>
            <div class="card-body"><canvas id="chart-operations"></canvas></div>
        </div>
    </div>
</div>

{{-- Fonctionnalités les plus utilisées --}}
<div class="row mb-4 animate-fadein" style="animation-delay: 0.22s">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header">Fonctionnalités les plus utilisées</div>
            <div class="card-body">
                <div id="no-features" class="text-center text-muted py-4 d-none">
                    Aucune donnée d'usage sur cette période
                </div>
                <canvas id="chart-top-features"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Performances & erreurs --}}
<div class="card animate-fadein" style="animation-delay: 0.27s">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Performances &amp; erreurs</span>
        <span class="small text-muted">
            p95 : <strong id="perf-p95">—</strong> ms &nbsp;·&nbsp;
            Taux d'erreur : <strong id="perf-error-rate">—</strong> &nbsp;·&nbsp;
            Requêtes : <strong id="perf-total">—</strong>
        </span>
    </div>
    <div class="card-body p-0">
        <div id="no-perf" class="text-center text-muted py-4 d-none">
            Aucune donnée de performance sur cette période
        </div>
        <table class="table table-hover align-middle mb-0" id="perf-table">
            <thead class="table-light">
                <tr>
                    <th>Fonctionnalité</th>
                    <th class="text-end">Requêtes</th>
                    <th class="text-end">Durée moy. (ms)</th>
                    <th class="text-end">Max (ms)</th>
                </tr>
            </thead>
            <tbody id="perf-body"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin.js"></script>
@endpush
