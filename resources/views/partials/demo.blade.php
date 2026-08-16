{{-- Démo : vidéo de présentation + simulation interactive : partagé entre la page Aide (/help) et la landing publique (/) --}}
<div class="help-section fade-up" id="demo">

    {{-- Vidéo de présentation --}}
    <h2 class="help-section-title text-center mb-1">Découvrez Oeil360 en 1 minute</h2>
    <p class="text-center text-muted mb-4" style="font-size:.9rem">
        Visite guidée de l'application : centraliser, visualiser, maîtriser.
    </p>

    <div class="demo-video mb-5">
        <video controls preload="none" playsinline
               poster="/images/oeil360-promo-poster.jpg">
            <source src="/videos/oeil360-promo.mp4" type="video/mp4">
            Votre navigateur ne permet pas de lire cette vidéo.
            <a href="/videos/oeil360-promo.mp4" download>Télécharger la vidéo (MP4)</a>.
        </video>
    </div>

    {{-- Simulation interactive --}}
    <h2 class="help-section-title text-center mb-1">Essayez maintenant</h2>
    <p class="text-center text-muted mb-4" style="font-size:.9rem">
        Simulation locale : aucune donnée réelle n'est modifiée.
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
                            Aucune entrée pour l'instant, ajoutez-en une !
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
