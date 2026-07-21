(() => {
    /* -------------------------------------------------------
       Données fictives — jamais envoyées au backend
       ------------------------------------------------------- */
    const FAKE_CATEGORIES = [
        { id: 1, name: 'Alimentation', type: 'expense' },
        { id: 2, name: 'Salaire',      type: 'income'  },
        { id: 3, name: 'Transport',    type: 'expense' },
        { id: 4, name: 'Loisirs',      type: 'expense' },
        { id: 5, name: 'Freelance',    type: 'income'  },
    ];

    const PIE_COLORS = ['#28c98a', '#1a2e4a', '#7b8fa6', '#06b6d4', '#f59e0b'];

    /* -------------------------------------------------------
       State local
       ------------------------------------------------------- */
    let demoTransactions = [];
    let demoChart = null;

    /* -------------------------------------------------------
       Intersection Observer — animations .fade-up
       ------------------------------------------------------- */
    function initScrollAnimations() {
        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 }
        );

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    }

    /* -------------------------------------------------------
       Helpers
       ------------------------------------------------------- */
    function fmtXOF(n) {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(n);
    }

    function getCategoryName(id) {
        return FAKE_CATEGORIES.find(c => c.id === Number(id))?.name ?? '—';
    }

    /* -------------------------------------------------------
       Rendu démo
       ------------------------------------------------------- */
    function renderDemo() {
        renderBalance();
        renderList();
        renderChart();
    }

    function renderBalance() {
        const balance = demoTransactions.reduce((sum, tx) => {
            return tx.sense === 'income' ? sum + tx.amount : sum - tx.amount;
        }, 0);

        const el = document.getElementById('demo-balance');
        el.textContent = fmtXOF(balance);
        el.className = 'demo-balance ' + (balance >= 0 ? 'amount-income' : 'amount-expense');
    }

    function renderList() {
        const list    = document.getElementById('demo-tx-list');
        const emptyEl = document.getElementById('demo-empty-msg');

        if (!demoTransactions.length) {
            emptyEl.style.display = '';
            list.querySelectorAll('.demo-tx-row').forEach(r => r.remove());
            return;
        }

        emptyEl.style.display = 'none';
        list.querySelectorAll('.demo-tx-row').forEach(r => r.remove());

        const visible = [...demoTransactions].reverse().slice(0, 5);
        visible.forEach(tx => {
            const row = document.createElement('div');
            row.className = 'demo-tx-row';

            const nameSpan = document.createElement('span');
            nameSpan.style.fontWeight = '500';
            nameSpan.textContent = tx.catName;

            const isIncome = tx.sense === 'income';
            const amountSpan = document.createElement('span');
            amountSpan.className = isIncome ? 'amount-income' : 'amount-expense';
            amountSpan.textContent = `${isIncome ? '+' : '−'} ${fmtXOF(tx.amount)}`;

            row.appendChild(nameSpan);
            row.appendChild(amountSpan);
            list.appendChild(row);
        });
    }

    function renderChart() {
        const canvas  = document.getElementById('demo-chart');
        const noChart = document.getElementById('demo-no-chart');

        const expenses = demoTransactions.filter(tx => tx.sense === 'expense');

        if (!expenses.length) {
            canvas.style.display = 'none';
            noChart.style.display = '';
            if (demoChart) { demoChart.destroy(); demoChart = null; }
            return;
        }

        canvas.style.display = '';
        noChart.style.display = 'none';

        const totals = {};
        expenses.forEach(tx => {
            totals[tx.catName] = (totals[tx.catName] ?? 0) + tx.amount;
        });

        const labels = Object.keys(totals);
        const data   = Object.values(totals);

        if (demoChart) demoChart.destroy();
        demoChart = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: PIE_COLORS.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
                    tooltip: { callbacks: { label: ctx => ` ${fmtXOF(ctx.raw)}` } },
                },
                cutout: '65%',
            },
        });
    }

    /* -------------------------------------------------------
       Initialisation démo
       ------------------------------------------------------- */
    function initDemo() {
        const addBtn   = document.getElementById('demo-add-btn');
        const resetBtn = document.getElementById('demo-reset-btn');
        const errorEl  = document.getElementById('demo-error');

        if (!addBtn) return;

        addBtn.addEventListener('click', () => {
            const amountRaw = document.getElementById('demo-amount').value;
            const amount    = parseFloat(amountRaw);
            const sense     = document.getElementById('demo-sense').value;
            const catId     = document.getElementById('demo-category').value;

            if (!amount || amount < 1) {
                errorEl.classList.remove('d-none');
                document.getElementById('demo-amount').focus();
                return;
            }

            errorEl.classList.add('d-none');

            demoTransactions.push({
                id:      Date.now(),
                amount,
                sense,
                catId:   Number(catId),
                catName: getCategoryName(catId),
            });

            document.getElementById('demo-amount').value = '';
            renderDemo();
        });

        resetBtn.addEventListener('click', () => {
            demoTransactions = [];
            if (demoChart) { demoChart.destroy(); demoChart = null; }
            renderDemo();
        });

        document.getElementById('demo-amount').addEventListener('keydown', e => {
            if (e.key === 'Enter') addBtn.click();
        });

        renderDemo();
    }

    /* -------------------------------------------------------
       Boot
       ------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', () => {
        initScrollAnimations();
        initDemo();
    });
})();
