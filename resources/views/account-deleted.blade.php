<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte supprimé · Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ assetVersion('css/app.css') }}">
    @include('partials.pwa')
    <style>
        html, body { height: 100%; background-color: var(--clr-bg); }
        .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .card-msg { background: var(--clr-surface); border-radius: var(--radius-modal); box-shadow: var(--shadow-modal); padding: 2.5rem 2rem; width: 100%; max-width: 520px; text-align: center; }
    </style>
</head>
<body>

<div class="wrap">
    <div class="card-msg">
        <div class="mb-3">
            <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" style="max-height:44px">
        </div>

        <i class="bi bi-check-circle" style="font-size:2.5rem;color:var(--clr-teal)"></i>

        <h1 class="h5 fw-bold mt-3 mb-2" style="color:var(--clr-navy)">Compte supprimé</h1>
        <p class="text-muted" style="font-size:.9rem">
            Ce compte a été supprimé et ses données effacées, conformément à ton droit à
            l'effacement (loi n°2017-20, APDP). Cet identifiant ne peut plus accéder à
            l'application.
        </p>
        <p class="text-muted" style="font-size:.85rem">
            Pour toute question, contacte-nous à
            <a href="mailto:oeil360finance@bytechnum.com" style="color:var(--clr-teal)">oeil360finance@bytechnum.com</a>.
        </p>

        <div class="d-flex flex-column gap-2 mt-2">
            <a href="{{ route('auth.relogin') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter avec un autre compte
            </a>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>

</body>
</html>
