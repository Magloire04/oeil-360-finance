function formatXOF(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

function formatDate(dateString) {
    if (!dateString) return '';
    const [year, month, day] = dateString.substring(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();
    const id = `toast-${Date.now()}`;
    const colorClass = type === 'success' ? 'text-bg-success' :
                       type === 'error'   ? 'text-bg-danger'  :
                       type === 'warning' ? 'text-bg-warning' : 'text-bg-info';

    container.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center ${colorClass} border-0" role="alert" aria-live="assertive" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);

    const toastEl = document.getElementById(id);
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

function createToastContainer() {
    const div = document.createElement('div');
    div.id = 'toast-container';
    div.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    div.style.zIndex = '1100';
    document.body.appendChild(div);
    return div;
}

function showError(message) {
    const existing = document.getElementById('global-error');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.id = 'global-error';
    div.className = 'alert alert-danger alert-dismissible fade show mx-3 mt-3';
    div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;

    const main = document.querySelector('main') || document.body;
    main.prepend(div);
}

function buildQueryString(params) {
    const filtered = Object.fromEntries(
        Object.entries(params).filter(([, v]) => v !== null && v !== undefined && v !== '')
    );
    return Object.keys(filtered).length ? '?' + new URLSearchParams(filtered).toString() : '';
}

window.formatXOF = formatXOF;
window.formatDate = formatDate;
window.showToast = showToast;
window.showError = showError;
window.buildQueryString = buildQueryString;
