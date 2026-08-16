<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consentement · Oeil 360° Finance</title>
    <link rel="icon" type="image/png" href="/images/oeil360-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ assetVersion('css/app.css') }}">
    @include('partials.pwa')
    <style>
        html, body { height: 100%; background-color: var(--clr-bg); }
        .consent-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .consent-card {
            background: var(--clr-surface);
            border-radius: var(--radius-modal);
            box-shadow: var(--shadow-modal);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 520px;
        }
    </style>
</head>
<body>

<div class="consent-wrapper">
    <div class="consent-card">
        <div class="text-center mb-4">
            <img src="/images/oeil360-logo-horizontal.png" alt="Oeil 360° Finance" style="max-height:44px">
        </div>

        <h1 class="h5 fw-bold mb-1" style="color:var(--clr-navy)">Avant de continuer</h1>
        <p class="text-muted mb-4" style="font-size:.875rem">
            Pour utiliser Oeil360 Finance, vous devez accepter notre politique de confidentialité
            conformément à la Loi n°2017-20 du Bénin (APDP).
        </p>

        @if (! empty($isUpdate))
            <div class="alert alert-info py-2 small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Notre politique de confidentialité a été mise à jour. Merci de la relire et de
                l'accepter à nouveau pour continuer à utiliser l'application.
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 small mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('consent.store') }}">
            @csrf

            <div class="card mb-4" style="background:var(--clr-teal-light);border:1px solid rgba(40,201,138,.3)">
                <div class="card-body py-3">
                    <h6 class="fw-semibold mb-2" style="color:var(--clr-navy)">Ce que nous collectons</h6>
                    <ul class="mb-0 small text-muted ps-3">
                        <li>Votre nom et adresse e-mail (via Auth0)</li>
                        <li>Vos données financières : transactions, comptes, catégories</li>
                        <li>La date et heure de votre consentement</li>
                    </ul>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="agree" id="agree"
                       value="1" {{ old('agree') ? 'checked' : '' }} required>
                <label class="form-check-label" for="agree" style="font-size:.875rem">
                    J'ai lu et j'accepte la
                    <a href="{{ url('/politique-confidentialite') }}" target="_blank" rel="noopener"
                       style="color:var(--clr-teal);font-weight:600">
                        politique de confidentialité <i class="bi bi-box-arrow-up-right" style="font-size:.75rem"></i>
                    </a>
                    d'Oeil360 Finance. Je comprends que mes données financières seront traitées
                    uniquement pour mon usage personnel.
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="submit-btn" disabled>
                <i class="bi bi-check-circle me-1"></i> Accepter et continuer
            </button>
        </form>

        <p class="text-center text-muted mt-3 mb-0" style="font-size:.78rem">
            Ce consentement est requis par la loi n°2017-20 (APDP, Bénin).
            Vous pouvez retirer votre consentement depuis "Mon compte".
        </p>
    </div>
</div>

<script>
    const checkbox = document.getElementById('agree');
    const btn = document.getElementById('submit-btn');
    checkbox.addEventListener('change', () => { btn.disabled = !checkbox.checked; });
    // Cas où la page est rechargée avec old() coché
    if (checkbox.checked) btn.disabled = false;
</script>

</body>
</html>
