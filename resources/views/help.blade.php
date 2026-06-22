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
        <a href="/" class="btn btn-primary px-4">
            <i class="bi bi-house me-1"></i> Aller au Dashboard
        </a>
        <a href="#demo" class="btn btn-outline-primary px-4">
            <i class="bi bi-play-circle me-1"></i> Essayer la démo
        </a>
    </div>
</div>

{{-- ============================================================
     SECTION 2 — C'EST QUOI ?
     ============================================================ --}}
<div class="help-section fade-up">
    <div class="row align-items-center g-4">
        <div class="col-md-6">
            <h2 class="help-section-title">C'est quoi Oeil360 Finance&nbsp;?</h2>
            <p class="text-muted mb-3">
                Oeil360 Finance est votre tableau de bord financier personnel.
                Enregistrez chaque revenu et chaque dépense, suivez vos soldes en temps réel
                et visualisez où va votre argent — le tout en un seul endroit.
            </p>
            <p class="text-muted mb-3">
                L'objectif&nbsp;: répondre à la question <strong style="color:var(--clr-navy)">"Combien j'ai, d'où ça vient, où ça va&nbsp;?"</strong>
                à tout moment, sans effort.
            </p>
            <p class="text-muted mb-0">
                Conçu pour le contexte UEMOA — uniquement en <strong>Franc CFA (XOF)</strong>,
                pour un suivi simple et précis.
            </p>
        </div>
        <div class="col-md-6">
            <div class="feature-grid">
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-arrow-left-right"></i></div>
                    <span class="feature-icon-label">Transactions</span>
                </div>
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-tags"></i></div>
                    <span class="feature-icon-label">Catégories</span>
                </div>
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-wallet2"></i></div>
                    <span class="feature-icon-label">Comptes</span>
                </div>
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-shuffle"></i></div>
                    <span class="feature-icon-label">Transferts</span>
                </div>
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-arrow-repeat"></i></div>
                    <span class="feature-icon-label">Récurrentes</span>
                </div>
                <div class="feature-icon-item">
                    <div class="feature-icon-box"><i class="bi bi-bar-chart-line"></i></div>
                    <span class="feature-icon-label">Dashboard</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     SECTION 3 — COMMENT ÇA MARCHE
     ============================================================ --}}
<div class="help-section fade-up">
    <h2 class="help-section-title text-center mb-4">Comment ça marche&nbsp;?</h2>
    <div class="help-steps">
        <div class="help-step">
            <span class="help-step-number">1</span>
            <div class="help-step-title">Ajoutez une transaction</div>
            <div class="help-step-desc">
                Montant, sens (revenu ou dépense), date, catégorie et compte.
                Chaque mouvement d'argent compte.
            </div>
        </div>
        <div class="help-step">
            <span class="help-step-number">2</span>
            <div class="help-step-title">Catégorisez</div>
            <div class="help-step-desc">
                Alimentation, Transport, Salaire… Créez vos propres catégories
                et organisez vos finances à votre façon.
            </div>
        </div>
        <div class="help-step">
            <span class="help-step-number">3</span>
            <div class="help-step-title">Gérez vos comptes</div>
            <div class="help-step-desc">
                Caisse, Mobile Money, Banque — suivez chaque compte séparément.
                Les transferts entre comptes ne faussent pas vos totaux.
            </div>
        </div>
        <div class="help-step">
            <span class="help-step-number">4</span>
            <div class="help-step-title">Analysez votre 360°</div>
            <div class="help-step-desc">
                Le dashboard vous donne une vue complète&nbsp;: soldes, graphiques
                et top catégories par période (jour, semaine, mois, année).
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     SECTION 4 — DÉMO INTERACTIVE
     ============================================================ --}}
<div class="help-section fade-up" id="demo">
    <h2 class="help-section-title text-center mb-1">Essayez maintenant</h2>
    <p class="text-center text-muted mb-4" style="font-size:.9rem">
        Simulation locale — aucune donnée réelle n'est modifiée.
    </p>

    <div class="demo-panel">
        {{-- Formulaire fictif --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-1"></i> Ajouter une entrée fictive
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Montant (XOF)</label>
                    <input type="number" id="demo-amount" class="form-control"
                           placeholder="ex. 5000" min="1" step="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sens</label>
                    <select id="demo-sense" class="form-select">
                        <option value="income">Revenu (entrée)</option>
                        <option value="expense">Dépense (sortie)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Catégorie</label>
                    <select id="demo-category" class="form-select">
                        <option value="1">Alimentation</option>
                        <option value="2">Salaire</option>
                        <option value="3">Transport</option>
                        <option value="4">Loisirs</option>
                        <option value="5">Freelance</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" id="demo-add-btn">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter
                    </button>
                    <button class="btn btn-outline-secondary" id="demo-reset-btn" title="Réinitialiser">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
                <div id="demo-error" class="text-danger small mt-2 d-none">
                    Veuillez saisir un montant valide (≥ 1 XOF).
                </div>
            </div>
        </div>

        {{-- Résultats live --}}
        <div>
            {{-- Solde fictif --}}
            <div class="card mb-3">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="kpi-icon"><i class="bi bi-wallet2"></i></span>
                    <div>
                        <div class="text-muted small">Solde fictif</div>
                        <div class="demo-balance amount-income" id="demo-balance">0 F CFA</div>
                    </div>
                </div>
            </div>

            {{-- Mini-graphique --}}
            <div class="card mb-3">
                <div class="card-header">Répartition des dépenses</div>
                <div class="card-body" style="position:relative;max-height:180px">
                    <div id="demo-no-chart" class="demo-empty">
                        Ajoutez des dépenses pour voir le graphique
                    </div>
                    <canvas id="demo-chart" style="display:none;max-height:160px"></canvas>
                </div>
            </div>

            {{-- Mini-liste --}}
            <div class="card">
                <div class="card-header">Dernières entrées fictives</div>
                <div class="card-body p-0">
                    <div id="demo-tx-list" style="padding:0 1rem">
                        <div class="demo-empty" id="demo-empty-msg">
                            Aucune entrée pour l'instant — ajoutez-en une !
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     SECTION 5 — FAQ
     ============================================================ --}}
<div class="help-section fade-up">
    <h2 class="help-section-title text-center mb-4">Questions fréquentes</h2>
    <div class="accordion" id="help-faq" style="max-width:720px;margin:0 auto">

        <div class="accordion-item" style="border:1px solid var(--clr-border);border-radius:var(--radius-card);margin-bottom:.75rem;overflow:hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-1"
                        style="font-weight:600;color:var(--clr-navy);background:var(--clr-surface)">
                    Mes données sont-elles sécurisées ?
                </button>
            </h2>
            <div id="faq-1" class="accordion-collapse collapse" data-bs-parent="#help-faq">
                <div class="accordion-body text-muted">
                    Oui. L'authentification est gérée par <strong>Auth0</strong>, un service de sécurité
                    professionnel. Vos données sont strictement isolées par compte&nbsp;: aucun autre
                    utilisateur ne peut accéder à vos transactions, catégories ou comptes.
                </div>
            </div>
        </div>

        <div class="accordion-item" style="border:1px solid var(--clr-border);border-radius:var(--radius-card);margin-bottom:.75rem;overflow:hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-2"
                        style="font-weight:600;color:var(--clr-navy);background:var(--clr-surface)">
                    Puis-je avoir plusieurs comptes ?
                </button>
            </h2>
            <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#help-faq">
                <div class="accordion-body text-muted">
                    Absolument. Vous pouvez créer autant de comptes que vous le souhaitez&nbsp;:
                    <strong>Caisse</strong> (argent liquide), <strong>Mobile Money</strong> (MTN, Moov…),
                    <strong>Compte bancaire</strong>, ou tout autre nom personnalisé. Chaque compte a
                    son propre solde calculé automatiquement.
                </div>
            </div>
        </div>

        <div class="accordion-item" style="border:1px solid var(--clr-border);border-radius:var(--radius-card);margin-bottom:.75rem;overflow:hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-3"
                        style="font-weight:600;color:var(--clr-navy);background:var(--clr-surface)">
                    Que se passe-t-il si je supprime une catégorie déjà utilisée ?
                </button>
            </h2>
            <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#help-faq">
                <div class="accordion-body text-muted">
                    Aucune donnée n'est perdue. Les catégories utilisées par des transactions
                    sont <strong>archivées</strong> (jamais supprimées définitivement). Vous pouvez
                    les restaurer à tout moment depuis la liste des catégories archivées.
                </div>
            </div>
        </div>

        <div class="accordion-item" style="border:1px solid var(--clr-border);border-radius:var(--radius-card);margin-bottom:.75rem;overflow:hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-4"
                        style="font-weight:600;color:var(--clr-navy);background:var(--clr-surface)">
                    Comment fonctionnent les transactions récurrentes ?
                </button>
            </h2>
            <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#help-faq">
                <div class="accordion-body text-muted">
                    Vous définissez une règle (montant + catégorie + fréquence&nbsp;: quotidienne,
                    hebdomadaire, mensuelle ou annuelle). Le système génère automatiquement chaque
                    occurrence à la date prévue. Chaque occurrence reste <strong>modifiable
                    indépendamment</strong> des autres — modifier une occurrence n'impacte pas les suivantes.
                </div>
            </div>
        </div>

        <div class="accordion-item" style="border:1px solid var(--clr-border);border-radius:var(--radius-card);overflow:hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq-5"
                        style="font-weight:600;color:var(--clr-navy);background:var(--clr-surface)">
                    La devise est-elle configurable ?
                </button>
            </h2>
            <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#help-faq">
                <div class="accordion-body text-muted">
                    Non, l'application fonctionne exclusivement en <strong>Franc CFA (XOF)</strong>.
                    Ce choix est délibéré&nbsp;: l'outil est conçu pour le contexte UEMOA (Bénin et
                    pays voisins), et une devise unique garantit la cohérence de tous vos calculs.
                </div>
            </div>
        </div>

    </div>
</div>

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
        <a href="/" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-house me-2"></i> Aller au Dashboard
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="/js/help.js"></script>
@endpush
