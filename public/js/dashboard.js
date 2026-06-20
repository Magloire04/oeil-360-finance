(() => {
    let chartPie = null;
    let chartLine = null;

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    // Calcul des bornes de période
    function getPeriodDates(period) {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

        if (period === 'day') {
            const s = fmt(now);
            return [s, s];
        }
        if (period === 'week') {
            const day = now.getDay() || 7;
            const mon = new Date(now); mon.setDate(now.getDate() - day + 1);
            const sun = new Date(mon); sun.setDate(mon.getDate() + 6);
            return [fmt(mon), fmt(sun)];
        }
        if (period === 'year') {
            return [`${now.getFullYear()}-01-01`, `${now.getFullYear()}-12-31`];
        }
        // month (default)
        const first = new Date(now.getFullYear(), now.getMonth(), 1);
        const last  = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return [fmt(first), fmt(last)];
    }

    // Rendu des cartes comptes
    function renderAccountCards(accounts) {
        const container = document.getElementById('account-cards');
        container.innerHTML = accounts.map(a => `
            <div class="col-12 col-sm-6">
                <div class="card h-100">
                    <div class="card-body py-2">
                        <div class="text-muted small">${esc(a.name)}</div>
                        <div class="fw-semibold">${formatXOF(a.balance)}</div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Graphique camembert — dépenses par catégorie
    function renderPieChart(data) {
        const canvas = document.getElementById('chart-expense-by-category');
        const noData = document.getElementById('no-expense-data');

        if (!data.length) {
            canvas.classList.add('d-none');
            noData.classList.remove('d-none');
            if (chartPie) { chartPie.destroy(); chartPie = null; }
            return;
        }

        canvas.classList.remove('d-none');
        noData.classList.add('d-none');

        const labels = data.map(d => d.category_name);
        const values = data.map(d => d.amount);
        const colors = ['#dc3545','#fd7e14','#ffc107','#198754','#0d6efd','#6610f2','#d63384','#20c997','#0dcaf0','#6c757d'];

        if (chartPie) chartPie.destroy();
        chartPie = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: colors.slice(0, values.length) }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${formatXOF(ctx.raw)}`,
                        },
                    },
                },
            },
        });
    }

    // Graphique courbe — évolution du solde
    function renderLineChart(data) {
        const canvas = document.getElementById('chart-balance-evolution');
        const labels = data.map(d => formatDate(d.date));
        const values = data.map(d => d.cumulative_balance);

        if (chartLine) chartLine.destroy();
        chartLine = new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Solde (XOF)',
                    data: values,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: { callback: v => formatXOF(v) },
                    },
                },
            },
        });
    }

    // Tableau des 10 dernières transactions
    function renderRecentTransactions(transactions) {
        const tbody = document.getElementById('recent-tx-body');
        const noTx  = document.getElementById('no-recent-tx');
        const table = document.getElementById('recent-tx-table');

        if (!transactions.length) {
            noTx.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        noTx.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = transactions.map(tx => {
            const isIncome = tx.sense === 'income';
            const badgeClass = isIncome ? 'badge-income' : 'badge-expense';
            const amountClass = isIncome ? 'amount-income' : 'amount-expense';
            const prefix = isIncome ? '+' : '-';
            return `<tr>
                <td>${esc(formatDate(tx.transaction_date))}</td>
                <td>${tx.note ? esc(tx.note) : '<span class="text-muted">—</span>'}</td>
                <td><span class="badge ${badgeClass}">${esc(tx.category?.name ?? '—')}</span></td>
                <td>${esc(tx.account?.name ?? '—')}</td>
                <td class="text-end ${amountClass}">${prefix} ${formatXOF(tx.amount)}</td>
            </tr>`;
        }).join('');
    }

    // Chargement principal
    async function loadDashboard(startDate, endDate) {
        try {
            const { data } = await api.get('/dashboard', { start_date: startDate, end_date: endDate });

            // Balances
            document.getElementById('total-balance').textContent = formatXOF(data.balances.total);
            renderAccountCards(data.balances.accounts);

            // Résumé période
            const net = data.period.net;
            const netEl = document.getElementById('period-net');
            document.getElementById('period-income').textContent  = formatXOF(data.period.income);
            document.getElementById('period-expense').textContent = formatXOF(data.period.expense);
            netEl.textContent  = formatXOF(net);
            netEl.className = 'h5 ' + (net >= 0 ? 'amount-income' : 'amount-expense');

            // Graphiques
            renderPieChart(data.expense_by_category);
            renderLineChart(data.balance_evolution);

            // Transactions récentes
            renderRecentTransactions(data.recent_transactions);
        } catch (err) {
            showError('Erreur lors du chargement du dashboard : ' + err.message);
        }
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        let [startDate, endDate] = getPeriodDates('month');

        // Pré-remplir les inputs date
        document.getElementById('start-date').value = startDate;
        document.getElementById('end-date').value   = endDate;

        // Boutons de période rapide
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                [startDate, endDate] = getPeriodDates(btn.dataset.period);
                document.getElementById('start-date').value = startDate;
                document.getElementById('end-date').value   = endDate;
                loadDashboard(startDate, endDate);
            });
        });

        // Bouton "Appliquer" pour dates custom
        document.getElementById('apply-period').addEventListener('click', () => {
            const s = document.getElementById('start-date').value;
            const e = document.getElementById('end-date').value;
            if (s && e) {
                document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                loadDashboard(s, e);
            }
        });

        loadDashboard(startDate, endDate);
    });
})();
