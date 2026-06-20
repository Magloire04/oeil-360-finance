(() => {
    let currentPage = 1;
    let lastMeta = {};
    let categories = [];
    let accounts = [];

    // ─── Échappement HTML (prévention XSS) ────────────────────────
    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ─── Chargement des données de référence ───────────────────────
    async function loadSelectOptions() {
        const [catRes, accRes] = await Promise.all([
            api.get('/categories'),
            api.get('/accounts'),
        ]);
        categories = catRes.data;
        accounts   = accRes.data;

        // Peupler les filtres
        populateSelect('filter-category', categories, c => ({ value: c.id, label: c.name }), 'Toutes catégories');
        populateSelect('filter-account',  accounts,   a => ({ value: a.id, label: a.name }), 'Tous comptes');

        // Peupler les selects de la modale
        populateSelect('tx-category', categories, c => ({ value: c.id, label: c.name }), '-- Choisir --');
        populateSelect('tx-account',  accounts,   a => ({ value: a.id, label: a.name }), '-- Choisir --');
    }

    function populateSelect(id, items, mapper, defaultLabel) {
        const el = document.getElementById(id);
        // Rebuild using DOM nodes — avoids innerHTML/insertAdjacentHTML XSS sinks
        el.replaceChildren();
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = defaultLabel;
        el.appendChild(defaultOpt);
        items.forEach(item => {
            const { value, label } = mapper(item);
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;
            el.appendChild(opt);
        });
    }

    // ─── Récupération des filtres actifs ───────────────────────────
    function getFilters() {
        return {
            start_date:  document.getElementById('filter-start-date').value || undefined,
            end_date:    document.getElementById('filter-end-date').value   || undefined,
            category_id: document.getElementById('filter-category').value   || undefined,
            account_id:  document.getElementById('filter-account').value    || undefined,
            sense:       document.getElementById('filter-sense').value      || undefined,
            q:           document.getElementById('filter-q').value          || undefined,
            page:        currentPage,
            per_page:    25,
        };
    }

    // ─── Chargement de la liste ─────────────────────────────────────
    async function loadTransactions() {
        try {
            const params = {};
            const raw = getFilters();
            Object.entries(raw).forEach(([k, v]) => { if (v !== undefined) params[k] = v; });

            const { data, meta } = await api.get('/transactions', params);
            lastMeta = meta;
            renderTable(data);
            renderPagination(meta);
        } catch (err) {
            showError('Erreur lors du chargement des transactions : ' + err.message);
        }
    }

    // ─── Rendu du tableau ──────────────────────────────────────────
    function renderTable(transactions) {
        const tbody  = document.getElementById('transactions-body');
        const noTx   = document.getElementById('no-transactions');
        const table  = document.getElementById('transactions-table');

        if (!transactions.length) {
            noTx.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noTx.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = transactions.map(tx => {
            const isIncome   = tx.sense === 'income';
            const badgeClass = isIncome ? 'badge-income' : 'badge-expense';
            const amtClass   = isIncome ? 'amount-income' : 'amount-expense';
            const prefix     = isIncome ? '+' : '-';
            // User-supplied text fields are escaped via esc() to prevent XSS.
            // Numeric/enum values (id, sense, amount, transaction_date) are safe without escaping.
            const noteHtml = tx.note
                ? esc(tx.note)
                : '<span class="text-muted">—</span>';
            return `<tr>
                <td>${esc(formatDate(tx.transaction_date))}</td>
                <td>${noteHtml}</td>
                <td>${esc(tx.category?.name ?? '—')}</td>
                <td>${esc(tx.account?.name ?? '—')}</td>
                <td><span class="badge ${badgeClass}">${isIncome ? 'Entrée' : 'Sortie'}</span></td>
                <td class="text-end ${amtClass}">${prefix} ${formatXOF(tx.amount)}</td>
                <td class="text-end">
                    <button class="btn btn-outline-secondary btn-sm btn-edit" data-id="${tx.id}" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm btn-delete" data-id="${tx.id}" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // ─── Pagination ────────────────────────────────────────────────
    function renderPagination(meta) {
        if (!meta) return;
        const info   = document.getElementById('pagination-info');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        info.textContent = `Page ${meta.current_page} / ${meta.last_page} — ${meta.total} résultat(s)`;
        btnPrev.disabled = meta.current_page <= 1;
        btnNext.disabled = meta.current_page >= meta.last_page;
    }

    // ─── Modale create/edit ─────────────────────────────────────────
    function resetModal() {
        document.getElementById('tx-id').value     = '';
        document.getElementById('tx-amount').value = '';
        document.getElementById('tx-sense').value  = '';
        const today = new Date().toISOString().substring(0, 10);
        document.getElementById('tx-date').value     = today;
        document.getElementById('tx-category').value = '';
        document.getElementById('tx-account').value  = '';
        document.getElementById('tx-note').value     = '';
        clearFieldErrors();
    }

    function clearFieldErrors() {
        ['amount','sense','transaction_date','category_id','account_id'].forEach(field => {
            const el = document.getElementById('err-' + field);
            if (el) el.textContent = '';
            const input = document.getElementById('tx-' + field.replace('_id','').replace('transaction_',''));
            if (input) input.classList.remove('is-invalid');
        });
    }

    function showFieldErrors(details) {
        clearFieldErrors();
        if (!details) return;
        Object.entries(details).forEach(([field, msgs]) => {
            const errEl = document.getElementById('err-' + field);
            const inputId = 'tx-' + field.replace('_id','').replace('transaction_','');
            const inputEl = document.getElementById(inputId);
            if (errEl)   { errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs; }
            if (inputEl) { inputEl.classList.add('is-invalid'); }
        });
    }

    function openModal(transaction = null) {
        resetModal();
        const title = document.getElementById('modal-title');

        if (transaction) {
            title.textContent = 'Modifier la transaction';
            document.getElementById('tx-id').value       = transaction.id;
            document.getElementById('tx-amount').value   = transaction.amount;
            document.getElementById('tx-sense').value    = transaction.sense;
            document.getElementById('tx-date').value     = transaction.transaction_date
                ? transaction.transaction_date.substring(0, 10) : '';
            document.getElementById('tx-category').value = transaction.category_id;
            document.getElementById('tx-account').value  = transaction.account_id;
            document.getElementById('tx-note').value     = transaction.note || '';
        } else {
            title.textContent = 'Nouvelle transaction';
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('transaction-modal')).show();
    }

    async function saveTransaction() {
        const id     = document.getElementById('tx-id').value;
        const amount = parseFloat(document.getElementById('tx-amount').value);

        // Validation côté client
        if (!amount || amount <= 0) {
            const errEl = document.getElementById('err-amount');
            document.getElementById('tx-amount').classList.add('is-invalid');
            if (errEl) errEl.textContent = 'Le montant doit être supérieur à 0.';
            return;
        }

        const payload = {
            amount,
            sense:            document.getElementById('tx-sense').value,
            transaction_date: document.getElementById('tx-date').value,
            category_id:      parseInt(document.getElementById('tx-category').value) || null,
            account_id:       parseInt(document.getElementById('tx-account').value)  || null,
            note:             document.getElementById('tx-note').value || null,
        };

        try {
            if (id) {
                await api.put(`/transactions/${id}`, payload);
                showToast('Transaction modifiée avec succès');
            } else {
                await api.post('/transactions', payload);
                showToast('Transaction créée avec succès');
            }
            bootstrap.Modal.getInstance(document.getElementById('transaction-modal')).hide();
            loadTransactions();
        } catch (err) {
            if (err.status === 422 && err.details) {
                showFieldErrors(err.details);
            } else {
                showError('Erreur : ' + err.message);
            }
        }
    }

    // ─── Suppression ───────────────────────────────────────────────
    async function deleteTransaction(id) {
        if (!confirm('Supprimer cette transaction ?')) return;
        try {
            await api.delete(`/transactions/${id}`);
            showToast('Transaction supprimée');
            loadTransactions();
        } catch (err) {
            showError('Erreur lors de la suppression : ' + err.message);
        }
    }

    // ─── Édition (récupère les données fraîches depuis l'API) ───────
    async function editTransaction(id) {
        try {
            const { data } = await api.get(`/transactions/${id}`);
            openModal(data);
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Écouteurs d'événements ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        await loadSelectOptions();
        loadTransactions();

        // Filtres
        ['filter-start-date','filter-end-date','filter-category','filter-account','filter-sense'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                currentPage = 1;
                loadTransactions();
            });
        });

        // Recherche texte avec debounce
        let searchTimeout;
        document.getElementById('filter-q').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadTransactions();
            }, 400);
        });

        // Réinitialiser les filtres
        document.getElementById('btn-reset-filters').addEventListener('click', () => {
            ['filter-start-date','filter-end-date','filter-q'].forEach(id => {
                document.getElementById(id).value = '';
            });
            ['filter-category','filter-account','filter-sense'].forEach(id => {
                document.getElementById(id).value = '';
            });
            currentPage = 1;
            loadTransactions();
        });

        // Nouvelle transaction
        document.getElementById('btn-new-transaction').addEventListener('click', () => openModal());

        // Enregistrer depuis la modale
        document.getElementById('btn-save-transaction').addEventListener('click', saveTransaction);

        // Pagination
        document.getElementById('btn-prev').addEventListener('click', () => {
            if (currentPage > 1) { currentPage--; loadTransactions(); }
        });
        document.getElementById('btn-next').addEventListener('click', () => {
            if (currentPage < (lastMeta.last_page || 1)) { currentPage++; loadTransactions(); }
        });

        // Délégation : edit + delete sur le tbody
        document.getElementById('transactions-body').addEventListener('click', e => {
            const editBtn   = e.target.closest('.btn-edit');
            const deleteBtn = e.target.closest('.btn-delete');
            if (editBtn)   editTransaction(editBtn.dataset.id);
            if (deleteBtn) deleteTransaction(deleteBtn.dataset.id);
        });
    });
})();
