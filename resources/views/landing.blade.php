@extends('layout-public')

@section('title', 'Découvrir')

@section('meta_description', 'Oeil360 Finance — reprenez le contrôle de vos finances personnelles en Franc CFA (XOF) : revenus, dépenses, comptes, transferts et charges récurrentes dans un seul tableau de bord.')

@section('content')

{{-- ============================================================
     SECTION 1 — HERO PUBLIC
     ============================================================ --}}
<div class="help-hero animate-fadein">
    <div class="help-hero-icon">
        <i class="bi bi-eye"></i>
    </div>
    <h1 class="h2 mb-2" style="font-weight:700;color:var(--clr-navy);letter-spacing:-0.02em">
        Voyez vos finances à 360°
    </h1>
    <p class="lead text-muted mb-4" style="max-width:560px;margin:0 auto">
        Oeil360 Finance centralise vos revenus, dépenses, comptes et charges récurrentes —
        en Franc CFA, sans tableur ni application dispersée.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('login') }}" class="btn btn-primary px-4">
            <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
        </a>
        <a href="#demo" class="btn btn-outline-primary px-4">
            <i class="bi bi-play-circle me-1"></i> Voir la démo
        </a>
    </div>
</div>

{{-- ============================================================
     SECTIONS 2 à 4 — contenu partagé avec la page Aide (/help)
     Source unique : resources/views/partials/
     ============================================================ --}}
<span id="fonctionnalites"></span>
@include('partials.what-is')

@include('partials.how-it-works')

@include('partials.demo')

{{-- ============================================================
     SECTION 5 — SÉCURITÉ & CONFORMITÉ (spécifique landing)
     ============================================================ --}}
<div class="help-section fade-up" id="securite">
    <h2 class="help-section-title text-center mb-1">Vos données vous appartiennent</h2>
    <p class="text-center text-muted mb-4" style="font-size:.9rem">
        Sécurité technique et conformité légale, dès le premier jour.
    </p>

    <div class="row g-4" style="max-width:980px;margin:0 auto">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="kpi-icon mx-auto mb-3" style="width:52px;height:52px;font-size:1.3rem">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="h6 mb-2" style="font-weight:600;color:var(--clr-navy)">
                        Authentification Auth0
                    </h3>
                    <p class="text-muted mb-0" style="font-size:.875rem">
                        La connexion est gérée par <strong>Auth0</strong>, un service de sécurité
                        professionnel. <strong>Aucun mot de passe n'est stocké</strong> par l'application.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="kpi-icon mx-auto mb-3" style="width:52px;height:52px;font-size:1.3rem">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <h3 class="h6 mb-2" style="font-weight:600;color:var(--clr-navy)">
                        Conforme APDP Bénin
                    </h3>
                    <p class="text-muted mb-0" style="font-size:.875rem">
                        Traitement des données personnelles conforme à la
                        <strong>loi n°2017-20</strong> du Bénin&nbsp;: consentement explicite,
                        droit d'accès et export de vos données.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="kpi-icon mx-auto mb-3" style="width:52px;height:52px;font-size:1.3rem">
                        <i class="bi bi-person-lock"></i>
                    </div>
                    <h3 class="h6 mb-2" style="font-weight:600;color:var(--clr-navy)">
                        Données cloisonnées
                    </h3>
                    <p class="text-muted mb-0" style="font-size:.875rem">
                        Vos transactions, catégories et comptes sont
                        <strong>strictement isolés</strong>&nbsp;: aucun autre utilisateur
                        n'y a accès.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <p class="text-center text-muted mt-4 mb-0" style="font-size:.85rem">
        <a href="{{ url('/politique-confidentialite') }}" style="color:var(--clr-teal)" target="_blank" rel="noopener">
            Consulter la politique de confidentialité
        </a>
    </p>
</div>

{{-- ============================================================
     SECTION 6 — FAQ (partagée)
     ============================================================ --}}
<span id="faq"></span>
@include('partials.faq')

{{-- ============================================================
     SECTION 7 — CTA FINAL
     ============================================================ --}}
<div class="fade-up mb-4">
    <div class="cta-section">
        <div class="kpi-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.4rem">
            <i class="bi bi-rocket-takeoff"></i>
        </div>
        <h2 class="mb-2">Prêt à prendre le contrôle&nbsp;?</h2>
        <p class="text-muted mb-4" style="max-width:460px;margin:0 auto 1.5rem">
            Connectez-vous en quelques secondes, ajoutez votre première transaction
            et commencez à voir clair dans vos finances.
        </p>
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter / Créer un compte
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ assetVersion('js/help.js') }}"></script>
@endpush
