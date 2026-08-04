@extends('layout')

@section('title', 'Comptes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Comptes</h1>
    <button class="btn btn-primary" id="btn-new-account">
        <i class="bi bi-plus-lg"></i> Nouveau compte
    </button>
</div>

{{-- Comptes actifs (cartes) --}}
<div class="row g-3 mb-4" id="account-cards">
    {{-- Rempli par JS --}}
</div>
<div id="no-accounts" class="text-center text-muted py-4 d-none">Aucun compte actif</div>

{{-- Comptes archivés (repliable) --}}
<div class="card">
    <div class="card-header">
        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#archived-accounts">
            <i class="bi bi-archive"></i> Comptes archivés
            <span class="badge bg-secondary ms-1" id="archived-count">0</span>
        </button>
    </div>
    <div class="collapse" id="archived-accounts">
        <div class="card-body p-0">
            <div id="no-archived" class="text-center text-muted py-4 d-none">Aucun compte archivé</div>
            <table class="table table-hover mb-0" id="archived-table">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th class="text-end">Solde initial</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="archived-body"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modale create/edit --}}
<div class="modal fade" id="account-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acc-modal-title">Nouveau compte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="account-form" novalidate>
                    <input type="hidden" id="acc-id">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="acc-name" class="form-control" required maxlength="255">
                        <div class="invalid-feedback" id="err-acc-name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select id="acc-type" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="cash">Espèces</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank">Compte bancaire</option>
                        </select>
                        <div class="invalid-feedback" id="err-acc-type"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Solde initial (XOF)</label>
                        <input type="number" id="acc-initial-balance" class="form-control" value="0" min="0" step="1">
                        <div class="invalid-feedback" id="err-acc-initial-balance"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-account">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ assetVersion('js/accounts.js') }}"></script>
@endpush
