<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité — Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body style="background:var(--clr-bg)">

<nav class="app-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand p-0" href="{{ url('/') }}">
            <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" height="38" class="navbar-logo">
        </a>
        @auth
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm ms-auto">
            <i class="bi bi-arrow-left me-1"></i> Retour à l'application
        </a>
        @endauth
    </div>
</nav>

<main class="container py-5" style="max-width:780px">

    <div class="mb-5">
        <h1 class="h3 fw-bold" style="color:var(--clr-navy)">Politique de confidentialité</h1>
        <p class="text-muted small">
            Version 1.0 — Dernière mise à jour : {{ date('d/m/Y') }}<br>
            Conformément à la Loi n°2017-20 du 20 avril 2017 portant code du numérique
            en République du Bénin (APDP).
        </p>
    </div>

    {{-- 1. Responsable du traitement --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">1. Responsable du traitement</h2>
        <div class="card">
            <div class="card-body text-muted">
                <p class="mb-1"><strong>Nom :</strong> <span class="text-warning">[À COMPLÉTER — Nom/Prénom ou Raison sociale]</span></p>
                <p class="mb-1"><strong>Adresse :</strong> <span class="text-warning">[À COMPLÉTER — Ville, Pays]</span></p>
                <p class="mb-0"><strong>Contact :</strong> <span class="text-warning">[À COMPLÉTER — adresse email de contact]</span></p>
            </div>
        </div>
    </section>

    {{-- 2. Données collectées --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">2. Données collectées</h2>
        <p class="text-muted">Dans le cadre de l'utilisation d'Oeil360 Finance, nous collectons les données suivantes :</p>
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold">Données d'identification</h6>
                <ul class="text-muted mb-3">
                    <li>Nom et prénom (fournis lors de la connexion via Auth0)</li>
                    <li>Adresse e-mail</li>
                    <li>Identifiant pseudonyme Auth0 (référence technique interne)</li>
                </ul>
                <h6 class="fw-semibold">Données financières personnelles</h6>
                <ul class="text-muted mb-3">
                    <li>Transactions : montants, dates, catégories, notes</li>
                    <li>Comptes et soldes (caisse, mobile money, banque…)</li>
                    <li>Transferts entre comptes</li>
                    <li>Transactions récurrentes programmées</li>
                </ul>
                <h6 class="fw-semibold">Données techniques</h6>
                <ul class="text-muted mb-0">
                    <li>Date et heure du consentement</li>
                    <li>Adresse IP et agent navigateur (données de session, chiffrées)</li>
                    <li>Date de dernière activité</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- 3. Finalités --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">3. Finalités du traitement</h2>
        <p class="text-muted">Vos données sont collectées exclusivement pour :</p>
        <ul class="text-muted">
            <li>Fournir les fonctionnalités de gestion financière personnelle</li>
            <li>Calculer et afficher vos soldes et statistiques</li>
            <li>Assurer la sécurité et la continuité du service</li>
            <li>Respecter nos obligations légales (conservation, audit)</li>
        </ul>
        <p class="text-muted">
            <strong>Aucune donnée n'est vendue, partagée ou utilisée à des fins commerciales ou publicitaires.</strong>
        </p>
    </section>

    {{-- 4. Base légale --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">4. Base légale</h2>
        <p class="text-muted">
            Le traitement de vos données repose sur votre <strong>consentement explicite</strong>
            recueilli lors de votre première connexion, conformément à l'article 4 de la Loi n°2017-20.
            Vous pouvez retirer ce consentement à tout moment depuis la section "Mon compte".
        </p>
    </section>

    {{-- 5. Durée de conservation --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">5. Durée de conservation</h2>
        <p class="text-muted">
            Vos données sont conservées pendant <strong>{{ env('DATA_RETENTION_YEARS', 5) }} ans</strong>
            à compter de votre dernière activité sur l'application. Au-delà de cette période d'inactivité,
            vos données pourront être supprimées ou anonymisées. Vous pouvez demander la suppression à
            tout moment en utilisant la fonctionnalité "Supprimer mon compte" dans votre espace personnel.
        </p>
    </section>

    {{-- 6. Droits --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">6. Vos droits</h2>
        <p class="text-muted">Conformément à la Loi n°2017-20, vous disposez des droits suivants :</p>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100" style="border-left:3px solid var(--clr-teal)!important">
                    <div class="card-body">
                        <h6 class="fw-semibold">Droit d'accès</h6>
                        <p class="text-muted small mb-0">
                            Exportez l'intégralité de vos données depuis "Mon compte → Exporter mes données".
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100" style="border-left:3px solid var(--clr-teal)!important">
                    <div class="card-body">
                        <h6 class="fw-semibold">Droit de rectification</h6>
                        <p class="text-muted small mb-0">
                            Modifiez vos transactions, catégories et comptes directement dans l'application.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100" style="border-left:3px solid var(--clr-expense)!important">
                    <div class="card-body">
                        <h6 class="fw-semibold">Droit à l'effacement</h6>
                        <p class="text-muted small mb-0">
                            Supprimez votre compte et toutes vos données depuis "Mon compte → Supprimer mon compte".
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100" style="border-left:3px solid var(--clr-expense)!important">
                    <div class="card-body">
                        <h6 class="fw-semibold">Droit au retrait du consentement</h6>
                        <p class="text-muted small mb-0">
                            Contactez-nous à l'adresse ci-dessous pour retirer votre consentement.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted mt-3 small">
            Pour exercer vos droits : <span class="text-warning">[À COMPLÉTER — adresse email de contact]</span>
        </p>
    </section>

    {{-- 7. Sécurité --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">7. Sécurité des données</h2>
        <ul class="text-muted">
            <li>Authentification sécurisée via Auth0 (OAuth 2.0 / PKCE)</li>
            <li>Sessions chiffrées côté serveur</li>
            <li>Données financières isolées par utilisateur (aucun partage inter-comptes)</li>
            <li>Connexion HTTPS obligatoire en production</li>
        </ul>
    </section>

    {{-- 8. Contact APDP --}}
    <section class="mb-4">
        <h2 class="h5 fw-semibold mb-2" style="color:var(--clr-navy)">8. Contact et autorité de contrôle</h2>
        <p class="text-muted">
            Pour toute question relative à vos données personnelles, contactez-nous à :
            <span class="text-warning">[À COMPLÉTER — adresse email de contact]</span>
        </p>
        <p class="text-muted">
            Vous pouvez également adresser une réclamation à l'<strong>Autorité de Protection des Données
            Personnelles (APDP)</strong> du Bénin si vous estimez que vos droits ne sont pas respectés.
        </p>
    </section>

    <hr>
    <p class="text-muted small text-center">
        © {{ date('Y') }} Oeil360 Finance — Version de la politique : 1.0
        @auth
            <br><a href="{{ url('/') }}" style="color:var(--clr-teal)">Retour à l'application</a>
        @endauth
    </p>

</main>

</body>
</html>
