<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ assetVersion('css/app.css') }}">
    <style>
        html, body { height: 100%; margin: 0; }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Panel gauche — navy */
        .login-panel-left {
            background-color: #1a2e4a;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 3rem 3.5rem;
            flex: 1 1 45%;
            position: relative;
            overflow: hidden;
        }

        .login-panel-left::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            background: rgba(40, 201, 138, 0.08);
            border-radius: 50%;
        }

        .login-panel-left::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            background: rgba(40, 201, 138, 0.05);
            border-radius: 50%;
        }

        .login-logo {
            max-height: 52px;
            width: auto;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .login-tagline {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            letter-spacing: -0.02em;
        }

        .login-tagline span {
            color: #28c98a;
        }

        .login-sub {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.9rem;
            line-height: 1.6;
            max-width: 340px;
            position: relative;
            z-index: 1;
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .login-divider-line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.12);
        }

        .login-divider-text {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Panel droit — formulaire */
        .login-panel-right {
            background-color: #f0f4f8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            flex: 1 1 55%;
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 2px 24px rgba(26, 46, 74, 0.09);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
        }

        .login-card-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1a2e4a;
            margin-bottom: 0.4rem;
            letter-spacing: -0.01em;
        }

        .login-card-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .btn-login {
            background-color: #28c98a;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            width: 100%;
            transition: background-color 0.18s, box-shadow 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .btn-login:hover {
            background-color: #1dac74;
            box-shadow: 0 4px 16px rgba(40, 201, 138, 0.35);
            color: #ffffff;
        }

        .login-note {
            color: #7b8fa6;
            font-size: 0.78rem;
            text-align: center;
            margin-top: 1.5rem;
        }

        /* Responsive — mobile en colonne unique */
        @media (max-width: 767px) {
            .login-wrapper { flex-direction: column; }

            .login-panel-left {
                flex: none;
                padding: 2rem 1.5rem 1.5rem;
                align-items: center;
                text-align: center;
            }

            .login-tagline { font-size: 1.3rem; }
            .login-sub { display: none; }
            .login-divider { display: none; }
            .login-logo { margin-bottom: 1rem; }

            .login-panel-right {
                flex: 1;
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- Panel gauche : branding --}}
    <div class="login-panel-left">
        <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" class="login-logo">

        <div class="login-tagline">
            Votre regard<br><span>précis</span> sur<br>vos finances.
        </div>

        <p class="login-sub">
            Suivez chaque entrée, chaque sortie, chaque transfert.<br>
            Un tableau de bord complet pour garder le contrôle.
        </p>

        <div class="login-divider">
            <span class="login-divider-line"></span>
            <span class="login-divider-text">Oeil 360° Finance</span>
            <span class="login-divider-line"></span>
        </div>
    </div>

    {{-- Panel droit : formulaire --}}
    <div class="login-panel-right">
        <div class="login-card">
            <div class="login-card-title">Bienvenue</div>
            <p class="login-card-subtitle">Connectez-vous pour accéder à votre espace financier personnel.</p>

            <a href="{{ url('/auth/login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Se connecter avec Auth0
            </a>

            <p class="login-note">
                Pas encore de compte&nbsp;? La connexion créera automatiquement votre espace.
            </p>
        </div>
    </div>

</div>

</body>
</html>
