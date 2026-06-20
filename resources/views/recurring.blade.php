@extends('layout')

@section('title', 'Transactions récurrentes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Transactions récurrentes</h1>
    <button class="btn btn-primary" id="btn-new-recurring">
        <i class="bi bi-plus-lg"></i> Nouvelle récurrente
    </button>
</div>

{{-- Récurrentes actives --}}
<div class="card mb-4">
    <div class="card-header">Actives</div>
    <div class="card-body p-0">
        <div id="no-active" class="text-center text-muted py-4 d-none">Aucune transaction récurrente active</div>
        <table class="table table-hover mb-0" id="active-table">
            <thead class="table-light">
                <tr>
                    <th>Catégorie</th>
                    <th>Compte</th>
                    <th>Sens</th>
                    <th>Fréquence</th>
                    <th class="text-end">Montant</th>
                    <th>Prochaine occurrence</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="active-body"></tbody>
        </table>
    </div>
</div>

{{-- Récurrentes inactives (repliable) --}}
<div class="card">
    <div class="card-header">
        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#inactive-section">
            <i class="bi bi-pause-circle"></i> Inactives
            <span class="badge bg-secondary ms-1" id="inactive-count">0</span>
        </button>
    </div>
    <div class="collapse" id="inactive-section">
        <div class="card-body p-0">
            <div id="no-inactive" class="text-center text-muted py-4 d-none">Aucune transaction récurrente inactive</div>
            <table class="table table-hover mb-0" id="inactive-table">
                <thead class="table-light">
                    <tr>
                        <th>Catégorie</th>
                        <th>Compte</th>
                        <th>Sens</th>
                        <th>Fréquence</th>
                        <th class="text-end">Montant</th>
                        <th>Prochaine occurrence</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="inactive-body"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modale create/edit --}}
<div class="modal fade" id="recurring-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rec-modal-title">Nouvelle récurrente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="recurring-form" novalidate>
                    <input type="hidden" id="rec-id">
                    <div class="mb-3">
                        <label class="form-label">Montant (XOF) <span class="text-danger">*</span></label>
                        <input type="number" id="rec-amount" class="form-control" min="1" step="1" required>
                        <div class="invalid-feedback" id="err-rec-amount"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sens <span class="text-danger">*</span></label>
                        <select id="rec-sense" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="income">Entrée (revenu)</option>
                            <option value="expense">Sortie (dépense)</option>
                        </select>
                        <div class="invalid-feedback" id="err-rec-sense"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fréquence <span class="text-danger">*</span></label>
                        <select id="rec-frequency" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="daily">Quotidienne</option>
                            <option value="weekly">Hebdomadaire</option>
                            <option value="monthly">Mensuelle</option>
                            <option value="yearly">Annuelle</option>
                        </select>
                        <div class="invalid-feedback" id="err-rec-frequency"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de début <span class="text-danger">*</span></label>
                        <input type="date" id="rec-start-date" class="form-control" required>
                        <div class="invalid-feedback" id="err-rec-start-date"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select id="rec-category" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-rec-category"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compte <span class="text-danger">*</span></label>
                        <select id="rec-account" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-rec-account"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea id="rec-note" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-recurring">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/recurring.js"></script>
@endpush
