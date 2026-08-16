@extends('layout')

@section('title', 'Catégories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Catégories</h1>
    <button class="btn btn-primary" id="btn-new-category">
        <i class="bi bi-plus-lg"></i> Nouvelle catégorie
    </button>
</div>

{{-- Catégories actives --}}
<div class="card mb-4">
    <div class="card-header">Catégories actives</div>
    <div class="card-body p-0">
        <div id="no-categories" class="text-center text-muted py-4 d-none">Aucune catégorie</div>
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="categories-table">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="categories-body"></tbody>
        </table>
        </div>
    </div>
</div>

{{-- Catégories archivées (repliable) --}}
<div class="card">
    <div class="card-header">
        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#archived-categories">
            <i class="bi bi-archive"></i> Catégories archivées
            <span class="badge bg-secondary ms-1" id="archived-count">0</span>
        </button>
    </div>
    <div class="collapse" id="archived-categories">
        <div class="card-body p-0">
            <div id="no-archived" class="text-center text-muted py-4 d-none">Aucune catégorie archivée</div>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="archived-table">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="archived-body"></tbody>
            </table>
            </div>
        </div>
    </div>
</div>

{{-- Modale create/edit --}}
<div class="modal fade" id="category-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cat-modal-title">Nouvelle catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="category-form" novalidate>
                    <input type="hidden" id="cat-id">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="cat-name" class="form-control" required maxlength="255">
                        <div class="invalid-feedback" id="err-cat-name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select id="cat-type" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="income">Revenu</option>
                            <option value="expense">Dépense</option>
                            <option value="both">Les deux</option>
                        </select>
                        <div class="invalid-feedback" id="err-cat-type"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-category">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ assetVersion('js/categories.js') }}"></script>
@endpush
