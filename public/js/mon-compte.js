(() => {
    const openBtn    = document.getElementById('btn-delete-account');
    const confirmBtn = document.getElementById('btn-confirm-delete');
    const input      = document.getElementById('delete-confirm-input');
    const errorEl    = document.getElementById('delete-error');

    if (!openBtn) return;

    const modal = new bootstrap.Modal(document.getElementById('delete-confirm-modal'));

    openBtn.addEventListener('click', () => {
        input.value = '';
        confirmBtn.disabled = true;
        errorEl.classList.add('d-none');
        modal.show();
        setTimeout(() => input.focus(), 300);
    });

    input.addEventListener('input', () => {
        confirmBtn.disabled = input.value.trim() !== 'SUPPRIMER';
        errorEl.classList.add('d-none');
    });

    confirmBtn.addEventListener('click', async () => {
        if (input.value.trim() !== 'SUPPRIMER') {
            errorEl.classList.remove('d-none');
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Suppression en cours…';

        try {
            const res = await fetch('/api/profile', {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
            });

            if (res.ok) {
                modal.hide();
                showToast('Votre compte a été supprimé. Vous allez être redirigé…', 'success');
                setTimeout(() => { window.location.href = '/auth/login'; }, 2000);
            } else {
                const json = await res.json().catch(() => null);
                const msg = json?.error?.message ?? 'Une erreur est survenue.';
                showToast(msg, 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Supprimer définitivement';
            }
        } catch {
            showToast('Erreur réseau. Réessayez.', 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Supprimer définitivement';
        }
    });
})();
