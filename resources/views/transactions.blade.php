@extends('layout')

@section('title', 'Transactions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Transactions</h1>
    <button class="btn btn-primary" id="btn-new-transaction">
        <i class="bi bi-plus-lg"></i> Nouvelle transaction
    </button>
</div>

{{-- Barre de filtres --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6 col-md-2">
                <input type="date" id="filter-start-date" class="form-control form-control-sm" placeholder="Du">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" id="filter-end-date" class="form-control form-control-sm" placeholder="Au">
            </div>
            <div class="col-6 col-md-2">
                <select id="filter-category" class="form-select form-select-sm">
                    <option value="">Toutes catégories</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filter-account" class="form-select form-select-sm">
                    <option value="">Tous comptes</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filter-sense" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="income">Entrées</option>
                    <option value="expense">Sorties</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="search" id="filter-q" class="form-control form-control-sm" placeholder="Rechercher...">
            </div>
        </div>
        <div class="mt-2">
            <button class="btn btn-outline-secondary btn-sm" id="btn-reset-filters">Réinitialiser</button>
        </div>
    </div>
</div>

{{-- Tableau --}}
<div class="card">
    <div class="card-body p-0">
        <div id="no-transactions" class="text-center text-muted py-5 d-none">
            Aucune transaction trouvée
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="transactions-table">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Note</th>
                        <th>Catégorie</th>
                        <th>Compte</th>
                        <th>Sens</th>
                        <th class="text-end">Montant</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="transactions-body"></tbody>
            </table>
        </div>
    </div>
    {{-- Pagination --}}
    <div class="card-footer d-flex justify-content-between align-items-center" id="pagination-footer">
        <span class="text-muted small" id="pagination-info"></span>
        <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" id="btn-prev" disabled>
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="btn-next" disabled>
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

{{-- Modale create/edit --}}
<div class="modal fade" id="transaction-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Nouvelle transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transaction-form" novalidate>
                    <input type="hidden" id="tx-id">

                    <div class="mb-3">
                        <label class="form-label">Montant (XOF) <span class="text-danger">*</span></label>
                        <input type="number" id="tx-amount" class="form-control" min="0.01" step="any" required>
                        <div class="invalid-feedback" id="err-amount"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sens <span class="text-danger">*</span></label>
                        <select id="tx-sense" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="income">Entrée (revenu)</option>
                            <option value="expense">Sortie (dépense)</option>
                        </select>
                        <div class="invalid-feedback" id="err-sense"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" id="tx-date" class="form-control" required>
                        <div class="invalid-feedback" id="err-transaction_date"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select id="tx-category" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-category_id"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Compte <span class="text-danger">*</span></label>
                        <select id="tx-account" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-account_id"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea id="tx-note" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-transaction">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/transactions.js"></script>
@endpush
