(() => {
    const TYPE_LABELS = { cash: 'Espèces', mobile_money: 'Mobile Money', bank: 'Compte bancaire' };
    const TYPE_ICONS  = { cash: 'bi-cash', mobile_money: 'bi-phone', bank: 'bi-bank' };

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    // ─── Chargement ─────────────────────────────────────────────────
    async function loadAccounts() {
        try {
            const { data } = await api.get('/accounts');
            const active   = data.filter(a => !a.is_archived);
            const archived = data.filter(a => a.is_archived);
            renderCards(active);
            renderArchivedTable(archived);
            document.getElementById('archived-count').textContent = archived.length;
        } catch (err) {
            showError('Erreur lors du chargement des comptes : ' + err.message);
        }
    }

    // ─── Cartes comptes actifs ──────────────────────────────────────
    function renderCards(accounts) {
        const container = document.getElementById('account-cards');
        const noData    = document.getElementById('no-accounts');

        if (!accounts.length) {
            container.innerHTML = '';
            noData.classList.remove('d-none');
            return;
        }

        noData.classList.add('d-none');
        container.innerHTML = accounts.map(a => {
            const icon  = TYPE_ICONS[a.type] || 'bi-wallet2';
            const label = TYPE_LABELS[a.type] || esc(a.type);
            const balanceClass = a.balance >= 0 ? 'amount-income' : 'amount-expense';

            return `<div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 card-balance">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="card-title mb-0">${esc(a.name)}</h6>
                                <small class="text-muted"><i class="bi ${icon}"></i> ${label}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary btn-edit" data-id="${a.id}" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-delete" data-id="${a.id}" title="Archiver / Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="h5 mb-0 ${balanceClass}">${formatXOF(a.balance)}</div>
                        <small class="text-muted">Solde initial : ${formatXOF(a.initial_balance)}</small>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ─── Tableau comptes archivés ────────────────────────────────────
    function renderArchivedTable(accounts) {
        const tbody  = document.getElementById('archived-body');
        const noData = document.getElementById('no-archived');
        const table  = document.getElementById('archived-table');

        if (!accounts.length) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noData.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = accounts.map(a => {
            const label = TYPE_LABELS[a.type] || esc(a.type);
            return `<tr>
                <td>${esc(a.name)}</td>
                <td>${label}</td>
                <td class="text-end">${formatXOF(a.initial_balance)}</td>
                <td class="text-end">
                    <button class="btn btn-outline-success btn-sm btn-restore" data-id="${a.id}" title="Restaurer">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // ─── Modale ─────────────────────────────────────────────────────
    function resetModal() {
        document.getElementById('acc-id').value              = '';
        document.getElementById('acc-name').value            = '';
        document.getElementById('acc-type').value            = '';
        document.getElementById('acc-initial-balance').value = '0';
        ['acc-name','acc-type','acc-initial-balance'].forEach(id => {
            document.getElementById(id).classList.remove('is-invalid');
        });
        ['err-acc-name','err-acc-type','err-acc-initial-balance'].forEach(id => {
            document.getElementById(id).textContent = '';
        });
    }

    function openModal(account = null) {
        resetModal();
        const title = document.getElementById('acc-modal-title');
        if (account) {
            title.textContent = 'Modifier le compte';
            document.getElementById('acc-id').value              = account.id;
            document.getElementById('acc-name').value            = account.name;
            document.getElementById('acc-type').value            = account.type;
            document.getElementById('acc-initial-balance').value = account.initial_balance;
        } else {
            title.textContent = 'Nouveau compte';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('account-modal')).show();
    }

    async function saveAccount() {
        const id              = document.getElementById('acc-id').value;
        const name            = document.getElementById('acc-name').value.trim();
        const type            = document.getElementById('acc-type').value;
        const initialBalance  = document.getElementById('acc-initial-balance').value;

        let valid = true;
        if (!name) {
            document.getElementById('acc-name').classList.add('is-invalid');
            document.getElementById('err-acc-name').textContent = 'Le nom est obligatoire.';
            valid = false;
        }
        if (!type) {
            document.getElementById('acc-type').classList.add('is-invalid');
            document.getElementById('err-acc-type').textContent = 'Le type est obligatoire.';
            valid = false;
        }
        if (!valid) return;

        const payload = { name, type, initial_balance: parseFloat(initialBalance) || 0 };

        try {
            if (id) {
                await api.put(`/accounts/${id}`, payload);
                showToast('Compte modifié');
            } else {
                await api.post('/accounts', payload);
                showToast('Compte créé');
            }
            bootstrap.Modal.getInstance(document.getElementById('account-modal')).hide();
            loadAccounts();
        } catch (err) {
            if (err.status === 422 && err.details) {
                Object.entries(err.details).forEach(([field, msgs]) => {
                    const inputId = {
                        name: 'acc-name',
                        type: 'acc-type',
                        initial_balance: 'acc-initial-balance',
                    }[field];
                    const errId = {
                        name: 'err-acc-name',
                        type: 'err-acc-type',
                        initial_balance: 'err-acc-initial-balance',
                    }[field];
                    if (inputId) document.getElementById(inputId)?.classList.add('is-invalid');
                    if (errId) {
                        const errEl = document.getElementById(errId);
                        if (errEl) errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    }
                });
            } else {
                showError('Erreur : ' + err.message);
            }
        }
    }

    // ─── Archiver / Supprimer ────────────────────────────────────────
    async function deleteAccount(id) {
        if (!confirm('Archiver ou supprimer ce compte ?')) return;
        try {
            const res = await api.delete(`/accounts/${id}`);
            if (res.data && res.data.archived) {
                showToast('Compte archivé (utilisé dans des transactions)', 'warning');
            } else {
                showToast('Compte supprimé');
            }
            loadAccounts();
        } catch (err) {
            showError('Erreur lors de la suppression : ' + err.message);
        }
    }

    // ─── Restaurer ────────────────────────────────────────────────────
    async function restoreAccount(id) {
        try {
            await api.post(`/accounts/${id}/restore`);
            showToast('Compte restauré');
            loadAccounts();
        } catch (err) {
            showError('Erreur lors de la restauration : ' + err.message);
        }
    }

    // ─── Édition ────────────────────────────────────────────────────
    async function editAccount(id) {
        try {
            const { data } = await api.get(`/accounts/${id}`);
            openModal(data);
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Écouteurs ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        loadAccounts();

        document.getElementById('btn-new-account').addEventListener('click', () => openModal());
        document.getElementById('btn-save-account').addEventListener('click', saveAccount);

        // Délégation sur les cartes actives (account-cards)
        document.getElementById('account-cards').addEventListener('click', e => {
            const editBtn   = e.target.closest('.btn-edit');
            const deleteBtn = e.target.closest('.btn-delete');
            if (editBtn)   editAccount(editBtn.dataset.id);
            if (deleteBtn) deleteAccount(deleteBtn.dataset.id);
        });

        // Délégation sur le tableau archivés
        document.getElementById('archived-body').addEventListener('click', e => {
            const restoreBtn = e.target.closest('.btn-restore');
            if (restoreBtn) restoreAccount(restoreBtn.dataset.id);
        });
    });
})();
