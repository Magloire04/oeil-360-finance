@extends('layout')

@section('title', 'Transferts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Transferts</h1>
    <button class="btn btn-primary" id="btn-new-transfer">
        <i class="bi bi-plus-lg"></i> Nouveau transfert
    </button>
</div>

{{-- Filtres --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-12 col-md-3">
                <input type="date" id="filter-start-date" class="form-control form-control-sm" placeholder="Du">
            </div>
            <div class="col-12 col-md-3">
                <input type="date" id="filter-end-date" class="form-control form-control-sm" placeholder="Au">
            </div>
            <div class="col-12 col-md-4">
                <select id="filter-account" class="form-select form-select-sm">
                    <option value="">Tous les comptes</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-secondary btn-sm w-100" id="btn-clear-filters">
                    <i class="bi bi-x-circle"></i> Effacer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tableau --}}
<div class="card">
    <div class="card-body p-0">
        <div id="no-transfers" class="text-center text-muted py-4 d-none">Aucun transfert trouvé</div>
        <table class="table table-hover mb-0" id="transfers-table">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>De</th>
                    <th></th>
                    <th>Vers</th>
                    <th class="text-end">Montant</th>
                    <th>Note</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="transfers-body"></tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted small" id="pagination-info"></span>
    <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-secondary" id="btn-prev" disabled>
            <i class="bi bi-chevron-left"></i> Précédent
        </button>
        <button class="btn btn-outline-secondary" id="btn-next" disabled>
            Suivant <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</div>

{{-- Modale create/edit --}}
<div class="modal fade" id="transfer-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tr-modal-title">Nouveau transfert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transfer-form" novalidate>
                    <input type="hidden" id="tr-id">
                    <div class="mb-3">
                        <label class="form-label">Montant (XOF) <span class="text-danger">*</span></label>
                        <input type="number" id="tr-amount" class="form-control" min="1" step="1" required>
                        <div class="invalid-feedback" id="err-tr-amount"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" id="tr-date" class="form-control" required>
                        <div class="invalid-feedback" id="err-tr-date"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compte source <span class="text-danger">*</span></label>
                        <select id="tr-from" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-tr-from"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compte destinataire <span class="text-danger">*</span></label>
                        <select id="tr-to" class="form-select" required>
                            <option value="">-- Choisir --</option>
                        </select>
                        <div class="invalid-feedback" id="err-tr-to"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea id="tr-note" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="alert alert-danger d-none" id="err-same-account">
                        Le compte source et le compte destinataire ne peuvent pas être identiques.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-transfer">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/transfers.js"></script>
@endpush
