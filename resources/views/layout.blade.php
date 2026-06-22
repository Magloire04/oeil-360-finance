<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oeil 360° Finance') — Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand p-0" href="/">
            <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" height="38" class="navbar-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}" href="/transactions">
                        <i class="bi bi-arrow-left-right"></i> Transactions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('categories*') ? 'active' : '' }}" href="/categories">
                        <i class="bi bi-tags"></i> Catégories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('accounts*') ? 'active' : '' }}" href="/accounts">
                        <i class="bi bi-wallet2"></i> Comptes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('transfers*') ? 'active' : '' }}" href="/transfers">
                        <i class="bi bi-shuffle"></i> Transferts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('recurring*') ? 'active' : '' }}" href="/recurring">
                        <i class="bi bi-arrow-repeat"></i> Récurrentes
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small d-none d-lg-inline">
                    {{ auth()->user()?->name ?? auth()->user()?->email ?? '' }}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid py-4">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="/js/utils.js"></script>
<script src="/js/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" integrity="sha384-vsrfeLOOY6KuIYKDlmVH5UiBmgIdB1oEf7p01YgWHuqmOHfZr374+odEv96n9tNC" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
