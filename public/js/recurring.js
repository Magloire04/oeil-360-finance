(() => {
    const FREQ_LABELS  = { daily: 'Quotidienne', weekly: 'Hebdomadaire', monthly: 'Mensuelle', yearly: 'Annuelle' };
    const SENSE_LABELS = { income: 'Entrée', expense: 'Sortie' };

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    // ─── Chargement selects ──────────────────────────────────────────
    async function loadSelectOptions() {
        try {
            const [catRes, accRes] = await Promise.all([
                api.get('/categories'),
                api.get('/accounts'),
            ]);

            const categories = catRes.data.filter(c => !c.is_archived);
            const accounts   = accRes.data.filter(a => !a.is_archived);

            populateSelect('rec-category', categories);
            populateSelect('rec-account', accounts);
        } catch (err) {
            showError('Erreur lors du chargement des listes : ' + err.message);
        }
    }

    function populateSelect(selectId, items) {
        const sel = document.getElementById(selectId);
        const firstOption = sel.options[0];
        sel.replaceChildren(firstOption);
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            sel.appendChild(opt);
        });
    }

    // ─── Chargement récurrentes ──────────────────────────────────────
    async function loadRecurring() {
        try {
            const { data } = await api.get('/recurring-transactions');
            const active   = data.filter(r => r.is_active);
            const inactive = data.filter(r => !r.is_active);
            renderTable('active-body', 'no-active', 'active-table', active, true);
            renderTable('inactive-body', 'no-inactive', 'inactive-table', inactive, false);
            document.getElementById('inactive-count').textContent = inactive.length;
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Rendu tableau ──────────────────────────────────────────────
    function renderTable(tbodyId, noDataId, tableId, items, isActive) {
        const tbody  = document.getElementById(tbodyId);
        const noData = document.getElementById(noDataId);
        const table  = document.getElementById(tableId);

        if (!items.length) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noData.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = items.map(r => {
            const senseClass = r.sense === 'income' ? 'badge-income' : 'badge-expense';
            const senseBadge = `<span class="badge ${senseClass}">${SENSE_LABELS[r.sense] || esc(r.sense)}</span>`;
            const freqLabel  = FREQ_LABELS[r.frequency] || esc(r.frequency);

            const toggleBtn = isActive
                ? `<button class="btn btn-outline-warning btn-sm btn-deactivate" data-id="${r.id}" title="Désactiver">
                       <i class="bi bi-pause"></i>
                   </button>`
                : `<button class="btn btn-outline-success btn-sm btn-activate" data-id="${r.id}" title="Activer">
                       <i class="bi bi-play"></i>
                   </button>`;

            return `<tr>
                <td>${esc(r.category?.name ?? '—')}</td>
                <td>${esc(r.account?.name ?? '—')}</td>
                <td>${senseBadge}</td>
                <td>${freqLabel}</td>
                <td class="text-end fw-semibold">${formatXOF(r.amount)}</td>
                <td>${esc(formatDate(r.next_occurrence_date))}</td>
                <td class="text-end">
                    <button class="btn btn-outline-secondary btn-sm btn-edit" data-id="${r.id}" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </button>
                    ${toggleBtn}
                    <button class="btn btn-outline-danger btn-sm btn-delete" data-id="${r.id}" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // ─── Modale ─────────────────────────────────────────────────────
    function resetModal() {
        document.getElementById('rec-id').value         = '';
        document.getElementById('rec-amount').value     = '';
        document.getElementById('rec-sense').value      = '';
        document.getElementById('rec-frequency').value  = '';
        document.getElementById('rec-start-date').value = new Date().toISOString().slice(0, 10);
        document.getElementById('rec-category').value   = '';
        document.getElementById('rec-account').value    = '';
        document.getElementById('rec-note').value       = '';

        ['rec-amount','rec-sense','rec-frequency','rec-start-date','rec-category','rec-account'].forEach(id => {
            document.getElementById(id).classList.remove('is-invalid');
        });
        ['err-rec-amount','err-rec-sense','err-rec-frequency','err-rec-start-date','err-rec-category','err-rec-account'].forEach(id => {
            document.getElementById(id).textContent = '';
        });
    }

    function openModal(recurring = null) {
        resetModal();
        const title = document.getElementById('rec-modal-title');
        if (recurring) {
            title.textContent = 'Modifier la récurrente';
            document.getElementById('rec-id').value         = recurring.id;
            document.getElementById('rec-amount').value     = recurring.amount;
            document.getElementById('rec-sense').value      = recurring.sense;
            document.getElementById('rec-frequency').value  = recurring.frequency;
            document.getElementById('rec-start-date').value = recurring.start_date;
            document.getElementById('rec-category').value   = recurring.category_id;
            document.getElementById('rec-account').value    = recurring.account_id;
            document.getElementById('rec-note').value       = recurring.note ?? '';
        } else {
            title.textContent = 'Nouvelle récurrente';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('recurring-modal')).show();
    }

    async function saveRecurring() {
        const id        = document.getElementById('rec-id').value;
        const amount    = parseFloat(document.getElementById('rec-amount').value);
        const sense     = document.getElementById('rec-sense').value;
        const frequency = document.getElementById('rec-frequency').value;
        const startDate = document.getElementById('rec-start-date').value;
        const category  = document.getElementById('rec-category').value;
        const account   = document.getElementById('rec-account').value;
        const note      = document.getElementById('rec-note').value.trim() || null;

        let valid = true;
        const required = [
            ['rec-amount',     'err-rec-amount',     amount > 0,  'Le montant doit être supérieur à 0.'],
            ['rec-sense',      'err-rec-sense',      !!sense,     'Le sens est obligatoire.'],
            ['rec-frequency',  'err-rec-frequency',  !!frequency, 'La fréquence est obligatoire.'],
            ['rec-start-date', 'err-rec-start-date', !!startDate, 'La date de début est obligatoire.'],
            ['rec-category',   'err-rec-category',   !!category,  'La catégorie est obligatoire.'],
            ['rec-account',    'err-rec-account',    !!account,   'Le compte est obligatoire.'],
        ];

        required.forEach(([inputId, errId, condition, msg]) => {
            if (!condition) {
                document.getElementById(inputId).classList.add('is-invalid');
                document.getElementById(errId).textContent = msg;
                valid = false;
            }
        });

        if (!valid) return;

        const payload = {
            amount,
            sense,
            frequency,
            start_date:   startDate,
            category_id:  parseInt(category, 10),
            account_id:   parseInt(account, 10),
            note,
        };

        try {
            if (id) {
                await api.put(`/recurring-transactions/${id}`, payload);
                showToast('Récurrente modifiée');
            } else {
                await api.post('/recurring-transactions', payload);
                showToast('Récurrente créée');
            }
            bootstrap.Modal.getInstance(document.getElementById('recurring-modal')).hide();
            loadRecurring();
        } catch (err) {
            if (err.status === 422 && err.details) {
                const fieldMap = {
                    amount:      { input: 'rec-amount',     err: 'err-rec-amount' },
                    sense:       { input: 'rec-sense',      err: 'err-rec-sense' },
                    frequency:   { input: 'rec-frequency',  err: 'err-rec-frequency' },
                    start_date:  { input: 'rec-start-date', err: 'err-rec-start-date' },
                    category_id: { input: 'rec-category',   err: 'err-rec-category' },
                    account_id:  { input: 'rec-account',    err: 'err-rec-account' },
                };
                Object.entries(err.details).forEach(([field, msgs]) => {
                    const map = fieldMap[field];
                    if (map) {
                        document.getElementById(map.input)?.classList.add('is-invalid');
                        const errEl = document.getElementById(map.err);
                        if (errEl) errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    }
                });
            } else {
                showError('Erreur : ' + err.message);
            }
        }
    }

    // ─── Toggle is_active ────────────────────────────────────────────
    async function toggleActive(id, activate) {
        try {
            await api.put(`/recurring-transactions/${id}`, { is_active: activate });
            showToast(activate ? 'Récurrente activée' : 'Récurrente désactivée');
            loadRecurring();
        } catch (err) {
            showError('Erreur : ' + err.message);
        }
    }

    // ─── Supprimer ───────────────────────────────────────────────────
    async function deleteRecurring(id) {
        if (!confirm('Supprimer cette récurrente ? Les transactions déjà générées seront conservées.')) return;
        try {
            await api.delete(`/recurring-transactions/${id}`);
            showToast('Récurrente supprimée');
            loadRecurring();
        } catch (err) {
            showError('Erreur lors de la suppression : ' + err.message);
        }
    }

    // ─── Édition ────────────────────────────────────────────────────
    async function editRecurring(id) {
        try {
            const { data } = await api.get(`/recurring-transactions/${id}`);
            openModal(data);
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Écouteurs ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        await loadSelectOptions();
        loadRecurring();

        document.getElementById('btn-new-recurring').addEventListener('click', () => openModal());
        document.getElementById('btn-save-recurring').addEventListener('click', saveRecurring);

        // Délégation sur les deux tableaux
        ['active-body', 'inactive-body'].forEach(tbodyId => {
            document.getElementById(tbodyId).addEventListener('click', e => {
                const editBtn       = e.target.closest('.btn-edit');
                const deleteBtn     = e.target.closest('.btn-delete');
                const deactivateBtn = e.target.closest('.btn-deactivate');
                const activateBtn   = e.target.closest('.btn-activate');

                if (editBtn)       editRecurring(editBtn.dataset.id);
                if (deleteBtn)     deleteRecurring(deleteBtn.dataset.id);
                if (deactivateBtn) toggleActive(deactivateBtn.dataset.id, false);
                if (activateBtn)   toggleActive(activateBtn.dataset.id, true);
            });
        });
    });
})();
