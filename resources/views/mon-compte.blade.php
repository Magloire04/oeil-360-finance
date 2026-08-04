@extends('layout')

@section('title', 'Mon compte')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-fadein">
    <h1 class="h3 mb-0">Mon compte</h1>
</div>

{{-- Informations du compte --}}
<div class="row g-4 mb-4 animate-fadein" style="animation-delay:.05s">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-person-circle me-1"></i> Informations personnelles
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Nom</dt>
                    <dd class="mb-3 fw-semibold">{{ auth()->user()?->name ?? '—' }}</dd>
                    <dt class="text-muted small">Email</dt>
                    <dd class="mb-3 fw-semibold">{{ auth()->user()?->email ?? '—' }}</dd>
                    <dt class="text-muted small">Membre depuis</dt>
                    <dd class="mb-0 fw-semibold">{{ auth()->user()?->created_at?->format('d/m/Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-shield-check me-1"></i> Consentement & confidentialité
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Consentement donné le</dt>
                    <dd class="mb-3">
                        @if(auth()->user()?->consent_given_at)
                            <span class="amount-income">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                {{ auth()->user()->consent_given_at->format('d/m/Y à H:i') }}
                            </span>
                        @else
                            <span class="amount-expense">Non fourni</span>
                        @endif
                    </dd>
                    <dt class="text-muted small">Version de la politique</dt>
                    <dd class="mb-3">{{ auth()->user()?->consent_version ?? '—' }}</dd>
                    <dt class="text-muted small">Politique de confidentialité</dt>
                    <dd class="mb-0">
                        <a href="/politique-confidentialite" target="_blank" rel="noopener"
                           style="color:var(--clr-teal)">
                            Consulter <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- Export des données --}}
<div class="card mb-4 animate-fadein" style="animation-delay:.1s">
    <div class="card-header">
        <i class="bi bi-download me-1"></i> Mes données (droit d'accès — APDP art. 496)
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Téléchargez l'intégralité de vos données personnelles et financières au format JSON.
        </p>
        <a href="{{ url('/mon-compte/export') }}" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> Exporter mes données (JSON)
        </a>
    </div>
</div>

{{-- Suppression du compte --}}
<div class="card animate-fadein" style="animation-delay:.15s;border-left:4px solid var(--clr-expense)!important">
    <div class="card-header" style="color:var(--clr-expense)">
        <i class="bi bi-trash3 me-1"></i> Supprimer mon compte (droit à l'effacement — APDP art. 501)
    </div>
    <div class="card-body">
        <p class="text-muted mb-2">
            La suppression efface <strong>définitivement et irréversiblement</strong> toutes vos données de l'application :
        </p>
        <ul class="text-muted small mb-3">
            <li>Profil (nom, email, identifiants)</li>
            <li>Toutes vos transactions, comptes, catégories, transferts et transactions récurrentes</li>
        </ul>
        <div class="alert alert-warning py-2 small mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Limitation Auth0 :</strong> cette action supprime vos données côté application uniquement.
            Votre identité Auth0 (compte de connexion) reste active sur auth0.com.
            Pour la supprimer également, connectez-vous sur
            <a href="https://auth0.com" target="_blank" rel="noopener">auth0.com</a>
            → Profile → Delete account.
        </div>
        <button class="btn btn-danger" id="btn-delete-account">
            <i class="bi bi-trash3 me-1"></i> Supprimer mon compte
        </button>
    </div>
</div>

{{-- Modale de confirmation --}}
<div class="modal fade" id="delete-confirm-modal" tabindex="-1" aria-labelledby="delete-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-modal-title" style="color:var(--clr-expense)">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Cette action est <strong>irréversible</strong>. Toutes vos données financières seront supprimées.</p>
                <p class="text-muted small">Pour confirmer, tapez <strong>SUPPRIMER</strong> ci-dessous :</p>
                <input type="text" id="delete-confirm-input" class="form-control"
                       placeholder="Tapez SUPPRIMER" autocomplete="off">
                <div id="delete-error" class="text-danger small mt-2 d-none">
                    Tapez exactement <strong>SUPPRIMER</strong> pour confirmer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete" disabled>
                    Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ assetVersion('js/mon-compte.js') }}"></script>
@endpush
