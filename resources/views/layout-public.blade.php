<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oeil 360° Finance') · Oeil 360° Finance</title>
    <meta name="description" content="@yield('meta_description', 'Oeil360 Finance : votre tableau de bord financier personnel en Franc CFA (XOF). Suivez revenus, dépenses, comptes et transactions récurrentes en un seul endroit.')">
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ assetVersion('css/app.css') }}">
    @include('partials.pwa')
</head>
<body>

{{-- Navbar publique : pas de lien vers les pages protégées, CTA de connexion --}}
<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand p-0" href="{{ url('/') }}">
            <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" height="38" class="navbar-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic"
                aria-controls="navbarPublic" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPublic">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#fonctionnalites">
                        <i class="bi bi-grid"></i> Fonctionnalités
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#demo">
                        <i class="bi bi-play-circle"></i> Démo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#securite">
                        <i class="bi bi-shield-check"></i> Sécurité
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">
                        <i class="bi bi-question-circle"></i> FAQ
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-light btn-sm fw-semibold">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid py-4">
    @yield('content')
</main>

<footer class="py-3 mt-2" style="border-top:1px solid var(--clr-border)">
    <div class="container-fluid text-center">
        <small class="text-muted">
            © {{ date('Y') }} Oeil360 Finance &nbsp;·&nbsp;
            <a href="{{ url('/politique-confidentialite') }}"
               style="color:var(--clr-muted)" target="_blank" rel="noopener">
                Politique de confidentialité
            </a>
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" integrity="sha384-vsrfeLOOY6KuIYKDlmVH5UiBmgIdB1oEf7p01YgWHuqmOHfZr374+odEv96n9tNC" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
