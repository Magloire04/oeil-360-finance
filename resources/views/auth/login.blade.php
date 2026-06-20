<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center" style="max-width: 400px; width: 100%;">
        <div class="card shadow-sm p-4">
            <div class="mb-4">
                <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" class="img-fluid" style="max-height: 60px;">
            </div>
            <h5 class="mb-1 fw-semibold">Bienvenue</h5>
            <p class="text-muted small mb-4">Connectez-vous pour accéder à votre espace financier personnel.</p>
            <a href="{{ url('/auth/login') }}" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </a>
        </div>
        <p class="text-muted small mt-3">
            Pas encore de compte ? La connexion créera automatiquement votre espace.
        </p>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
</body>
</html>
