{{-- Questions fréquentes (FAQ) : partagé entre la page Aide (/help) et la landing publique (/) --}}
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
                    indépendamment</strong> des autres : modifier une occurrence n'impacte pas les suivantes.
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
