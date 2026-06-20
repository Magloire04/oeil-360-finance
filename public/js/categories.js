(() => {
    const TYPE_LABELS = { income: 'Revenu', expense: 'Dépense', both: 'Les deux' };
    const TYPE_BADGES = { income: 'bg-success', expense: 'bg-danger', both: 'bg-secondary' };

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    // ─── Chargement ─────────────────────────────────────────────────
    async function loadCategories() {
        try {
            const { data } = await api.get('/categories');
            const active   = data.filter(c => !c.is_archived);
            const archived = data.filter(c => c.is_archived);
            renderTable('categories-body', 'no-categories', 'categories-table', active, false);
            renderTable('archived-body', 'no-archived', 'archived-table', archived, true);
            document.getElementById('archived-count').textContent = archived.length;
        } catch (err) {
            showError('Erreur lors du chargement des catégories : ' + err.message);
        }
    }

    // ─── Rendu tableau ──────────────────────────────────────────────
    function renderTable(tbodyId, noDataId, tableId, items, isArchived) {
        const tbody = document.getElementById(tbodyId);
        const noData = document.getElementById(noDataId);
        const table  = document.getElementById(tableId);

        if (!items.length) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noData.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = items.map(c => {
            const badge = `<span class="badge ${TYPE_BADGES[c.type]}">${TYPE_LABELS[c.type] || esc(c.type)}</span>`;
            const actions = isArchived
                ? `<button class="btn btn-outline-success btn-sm btn-restore" data-id="${c.id}" title="Restaurer">
                       <i class="bi bi-arrow-counterclockwise"></i>
                   </button>`
                : `<button class="btn btn-outline-secondary btn-sm btn-edit" data-id="${c.id}" title="Modifier">
                       <i class="bi bi-pencil"></i>
                   </button>
                   <button class="btn btn-outline-danger btn-sm btn-delete" data-id="${c.id}" title="Archiver / Supprimer">
                       <i class="bi bi-trash"></i>
                   </button>`;

            return `<tr>
                <td>${esc(c.name)}</td>
                <td>${badge}</td>
                <td class="text-end">${actions}</td>
            </tr>`;
        }).join('');
    }

    // ─── Modale ─────────────────────────────────────────────────────
    function resetModal() {
        document.getElementById('cat-id').value   = '';
        document.getElementById('cat-name').value = '';
        document.getElementById('cat-type').value = '';
        ['cat-name','cat-type'].forEach(id => document.getElementById(id).classList.remove('is-invalid'));
        ['err-cat-name','err-cat-type'].forEach(id => { document.getElementById(id).textContent = ''; });
    }

    function openModal(category = null) {
        resetModal();
        const title = document.getElementById('cat-modal-title');
        if (category) {
            title.textContent = 'Modifier la catégorie';
            document.getElementById('cat-id').value   = category.id;
            document.getElementById('cat-name').value = category.name;
            document.getElementById('cat-type').value = category.type;
        } else {
            title.textContent = 'Nouvelle catégorie';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('category-modal')).show();
    }

    async function saveCategory() {
        const id   = document.getElementById('cat-id').value;
        const name = document.getElementById('cat-name').value.trim();
        const type = document.getElementById('cat-type').value;

        // Validation client minimale
        let valid = true;
        if (!name) {
            document.getElementById('cat-name').classList.add('is-invalid');
            document.getElementById('err-cat-name').textContent = 'Le nom est obligatoire.';
            valid = false;
        }
        if (!type) {
            document.getElementById('cat-type').classList.add('is-invalid');
            document.getElementById('err-cat-type').textContent = 'Le type est obligatoire.';
            valid = false;
        }
        if (!valid) return;

        const payload = { name, type };

        try {
            if (id) {
                await api.put(`/categories/${id}`, payload);
                showToast('Catégorie modifiée');
            } else {
                await api.post('/categories', payload);
                showToast('Catégorie créée');
            }
            bootstrap.Modal.getInstance(document.getElementById('category-modal')).hide();
            loadCategories();
        } catch (err) {
            if (err.status === 422 && err.details) {
                Object.entries(err.details).forEach(([field, msgs]) => {
                    const inputId = field === 'name' ? 'cat-name' : 'cat-type';
                    const errId   = field === 'name' ? 'err-cat-name' : 'err-cat-type';
                    document.getElementById(inputId)?.classList.add('is-invalid');
                    const errEl = document.getElementById(errId);
                    if (errEl) errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                });
            } else {
                showError('Erreur : ' + err.message);
            }
        }
    }

    // ─── Archiver / Supprimer ────────────────────────────────────────
    async function deleteCategory(id) {
        if (!confirm('Archiver ou supprimer cette catégorie ?')) return;
        try {
            const res = await api.delete(`/categories/${id}`);
            // Si archivée : data.archived = true (200) ; si supprimée : 204 → data = null
            if (res.data && res.data.archived) {
                showToast('Catégorie archivée (utilisée dans des transactions)', 'warning');
            } else {
                showToast('Catégorie supprimée');
            }
            loadCategories();
        } catch (err) {
            showError('Erreur lors de la suppression : ' + err.message);
        }
    }

    // ─── Restaurer ────────────────────────────────────────────────────
    async function restoreCategory(id) {
        try {
            await api.post(`/categories/${id}/restore`);
            showToast('Catégorie restaurée');
            loadCategories();
        } catch (err) {
            showError('Erreur lors de la restauration : ' + err.message);
        }
    }

    // ─── Édition ────────────────────────────────────────────────────
    async function editCategory(id) {
        try {
            const { data } = await api.get(`/categories/${id}`);
            openModal(data);
        } catch (err) {
            showError('Erreur lors du chargement : ' + err.message);
        }
    }

    // ─── Écouteurs ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        loadCategories();

        document.getElementById('btn-new-category').addEventListener('click', () => openModal());
        document.getElementById('btn-save-category').addEventListener('click', saveCategory);

        // Délégation sur les tableaux actives + archivées
        ['categories-body', 'archived-body'].forEach(tbodyId => {
            document.getElementById(tbodyId).addEventListener('click', e => {
                const editBtn    = e.target.closest('.btn-edit');
                const deleteBtn  = e.target.closest('.btn-delete');
                const restoreBtn = e.target.closest('.btn-restore');
                if (editBtn)    editCategory(editBtn.dataset.id);
                if (deleteBtn)  deleteCategory(deleteBtn.dataset.id);
                if (restoreBtn) restoreCategory(restoreBtn.dataset.id);
            });
        });
    });
})();
