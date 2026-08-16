(() => {
    let accounts = [];
    let currentPage = 1;
    let lastPage = 1;

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    // ─── Chargement comptes (pour selects) ──────────────────────────
    async function loadAccounts() {
        try {
            const { data } = await api.get('/accounts');
            accounts = data.filter(a => !a.is_archived);
            populateAccountSelects();
        } catch (err) {
            showError('Erreur lors du chargement des comptes : ' + err.message);
        }
    }

    function populateAccountSelects() {
        ['filter-account', 'tr-from', 'tr-to'].forEach(selectId => {
            const sel = document.getElementById(selectId);
            // Conserver la première option (<option value="">...)
            const firstOption = sel.options[0];
            sel.replaceChildren(firstOption);
            accounts.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.name;
                sel.appendChild(opt);
            });
        });
    }

    // ─── Filtres ────────────────────────────────────────────────────
    function getFilters() {
        return {
            start_date: document.getElementById('filter-start-date').value || null,
            end_date:   document.getElementById('filter-end-date').value   || null,
            account_id: document.getElementById('filter-account').value    || null,
            page:       currentPage,
        };
    }

    // ─── Chargement transferts ───────────────────────────────────────
    async function loadTransfers() {
        try {
            const params = getFilters();
            const { data, meta } = await api.get('/transfers', params);
            lastPage = meta?.last_page ?? 1;
            renderTable(data);
            renderPagination(meta);
        } catch (err) {
            showError('Erreur lors du chargement des transferts : ' + err.message);
        }
    }

    // ─── Rendu tableau ──────────────────────────────────────────────
    function renderTable(items) {
        const tbody   = document.getElementById('transfers-body');
        const noData  = document.getElementById('no-transfers');
        const table   = document.getElementById('transfers-table');

        if (!items.length) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noData.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = items.map(t => `<tr>
            <td>${esc(formatDate(t.transfer_date))}</td>
            <td>${esc(t.from_account?.name ?? '-')}</td>
            <td><i class="bi bi-arrow-right text-muted"></i></td>
            <td>${esc(t.to_account?.name ?? '-')}</td>
            <td class="text-end fw-semibold">${formatXOF(t.amount)}</td>
            <td class="text-muted">${esc(t.note ?? '')}</td>
            <td class="text-end">
                <button class="btn btn-outline-secondary btn-sm btn-edit" data-id="${t.id}" title="Modifier">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm btn-delete" data-id="${t.id}" title="Supprimer">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`).join('');
    }

    // ─── Pagination ─────────────────────────────────────────────────
    function renderPagination(meta) {
        if (!meta) return;
        const info    = document.getElementById('pagination-info');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        info.textContent = `Page ${meta.current_page} sur ${meta.last_page} · ${meta.total} transfert(s)`;
        btnPrev.disabled = meta.current_page <= 1;
        btnNext.disabled = meta.current_page >= meta.last_page;
    }

    // ─── Modale ─────────────────────────────────────────────────────
    function resetModal() {
        document.getElementById('tr-id').value     = '';
        document.getElementById('tr-amount').value = '';
        document.getElementById('tr-date').value   = new Date().toISOString().slice(0, 10);
        document.getElementById('tr-from').value   = '';
        document.getElementById('tr-to').value     = '';
        document.getElementById('tr-note').value   = '';

        ['tr-amount','tr-date','tr-from','tr-to'].forEach(id => {
            document.getElementById(id).classList.remove('is-invalid');
        });
        ['err-tr-amount','err-tr-date','err-tr-from','err-tr-to'].forEach(id => {
            document.getElementById(id).textContent = '';
        });
        document.getElementById('err-same-account').classList.add('d-none');
    }

    function openModal(transfer = null) {
        resetModal();
        const title = document.getElementById('tr-modal-title');
        if (transfer) {
            title.textContent = 'Modifier le transfert';
            document.getElementById('tr-id').value     = transfer.id;
            document.getElementById('tr-amount').value = transfer.amount;
            document.getElementById('tr-date').value   = transfer.transfer_date;
            document.getElementById('tr-from').value   = transfer.from_account_id;
            document.getElementById('tr-to').value     = transfer.to_account_id;
            document.getElementById('tr-note').value   = transfer.note ?? '';
        } else {
            title.textContent = 'Nouveau transfert';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('transfer-modal')).show();
    }

    async function saveTransfer() {
        const id     = document.getElementById('tr-id').value;
        const amount = parseFloat(document.getElementById('tr-amount').value);
        const date   = document.getElementById('tr-date').value;
        const from   = document.getElementById('tr-from').value;
        const to     = document.getElementById('tr-to').value;
        const note   = document.getElementById('tr-note').value.trim() || null;

        // Validation client
        let valid = true;

        if (!amount || amount <= 0) {
            document.getElementById('tr-amount').classList.add('is-invalid');
            document.getElementById('err-tr-amount').textContent = 'Le montant doit être supérieur à 0.';
            valid = false;
        }
        if (!date) {
            document.getElementById('tr-date').classList.add('is-invalid');
            document.getElementById('err-tr-date').textContent = 'La date est obligatoire.';
            valid = false;
        }
        if (!from) {
            document.getElementById('tr-from').classList.add('is-invalid');
            document.getElementById('err-tr-from').textContent = 'Le compte source est obligatoire.';
            valid = false;
        }
        if (!to) {
            document.getElementById('tr-to').classList.add('is-invalid');
            document.getElementById('err-tr-to').textContent = 'Le compte destinataire est obligatoire.';
            valid = false;
        }
        if (from && to && from === to) {
            document.getElementById('err-same-account').classList.remove('d-none');
            valid = false;
        } else {
            document.getElementById('err-same-account').classList.add('d-none');
        }
        if (!valid) return;

        const payload = {
            amount,
            transfer_date:    date,
            from_account_id:  parseInt(from, 10),
            to_account_id:    parseInt(to, 10),
            note,
        };

        try {
            if (id) {
                await api.put(`/transfers/${id}`, payload);
                showToast('Transfert modifié');
            } else {
                await api.post('/transfers', payload);
                showToast('Transfert créé');
            }
            bootstrap.Modal.getInstance(document.getElementById('transfer-modal')).hide();
            loadTransfers();
        } catch (err) {
            if (err.status === 422 && err.details) {
                const fieldMap = {
                    amount:          { input: 'tr-amount', err: 'err-tr-amount' },
                    transfer_date:   { input: 'tr-date',   err: 'err-tr-date' },
                    from_account_id: { input: 'tr-from',   err: 'err-tr-from' },
                    to_account_id:   { input: 'tr-to',     err: 'err-tr-to' },
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

    // ─── Supprimer ───────────────────────────────────────────────────
    async function deleteTransfer(id) {
        if (!confirm('Supprimer ce transfert ?')) return;
        try {
            await api.delete(`/transfers/${id}`);
            showToast('Transfert supprimé');
            loadTransfers();
        } catch (err) {
            showError('Erreur lors de la suppression : ' + err.message);
        }
    }

    // ─── Édition ────────────────────────────────────────────────────
    async function editTransfer(id) {
        try {
            const { data } = await api.get(`/transfers/${id}`);
            openModal(data);
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Écouteurs ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        await loadAccounts();
        loadTransfers();

        document.getElementById('btn-new-transfer').addEventListener('click', () => openModal());
        document.getElementById('btn-save-transfer').addEventListener('click', saveTransfer);

        document.getElementById('btn-clear-filters').addEventListener('click', () => {
            document.getElementById('filter-start-date').value = '';
            document.getElementById('filter-end-date').value   = '';
            document.getElementById('filter-account').value    = '';
            currentPage = 1;
            loadTransfers();
        });

        document.getElementById('btn-prev').addEventListener('click', () => {
            if (currentPage > 1) { currentPage--; loadTransfers(); }
        });
        document.getElementById('btn-next').addEventListener('click', () => {
            if (currentPage < lastPage) { currentPage++; loadTransfers(); }
        });

        // Filtres → reset page et recharger
        ['filter-start-date','filter-end-date','filter-account'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                currentPage = 1;
                loadTransfers();
            });
        });

        // Délégation sur le tbody
        document.getElementById('transfers-body').addEventListener('click', e => {
            const editBtn   = e.target.closest('.btn-edit');
            const deleteBtn = e.target.closest('.btn-delete');
            if (editBtn)   editTransfer(editBtn.dataset.id);
            if (deleteBtn) deleteTransfer(deleteBtn.dataset.id);
        });
    });
})();
