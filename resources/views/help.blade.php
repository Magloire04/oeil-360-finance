@extends('layout')

@section('title', 'Aide')

@section('content')

{{-- ============================================================
     SECTION 1 — HERO
     ============================================================ --}}
<div class="help-hero animate-fadein">
    <div class="help-hero-icon">
        <i class="bi bi-eye"></i>
    </div>
    <h1 class="h2 mb-2" style="font-weight:700;color:var(--clr-navy);letter-spacing:-0.02em">
        Bienvenue dans Oeil360 Finance
    </h1>
    <p class="lead text-muted mb-4" style="max-width:520px;margin:0 auto">
        Votre outil pour reprendre le contrôle de vos finances personnelles.
        Comprenez en 2 minutes comment tout fonctionne.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="/dashboard" class="btn btn-primary px-4">
            <i class="bi bi-house me-1"></i> Aller au Dashboard
        </a>
        <a href="#demo" class="btn btn-outline-primary px-4">
            <i class="bi bi-play-circle me-1"></i> Essayer la démo
        </a>
    </div>
</div>

{{-- ============================================================
     SECTIONS 2 à 5 — contenu partagé avec la landing publique (/)
     Source unique : resources/views/partials/
     ============================================================ --}}
@include('partials.what-is')
@include('partials.how-it-works')
@include('partials.demo')
@include('partials.faq')

{{-- ============================================================
     SECTION 6 — CTA FINAL
     ============================================================ --}}
<div class="fade-up mb-4">
    <div class="cta-section">
        <div class="kpi-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.4rem">
            <i class="bi bi-rocket-takeoff"></i>
        </div>
        <h2 class="mb-2">Prêt à prendre le contrôle&nbsp;?</h2>
        <p class="text-muted mb-4" style="max-width:440px;margin:0 auto 1.5rem">
            Vos finances attendent. Ouvrez le dashboard, ajoutez votre première
            transaction et commencez à voir clair.
        </p>
        <a href="/dashboard" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-house me-2"></i> Aller au Dashboard
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ assetVersion('js/help.js') }}"></script>
@endpush
