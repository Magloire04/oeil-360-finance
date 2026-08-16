(() => {
    let chartPie = null;
    let chartBar = null;

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

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
        const first = new Date(now.getFullYear(), now.getMonth(), 1);
        const last  = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return [fmt(first), fmt(last)];
    }

    function renderKpis(kpis) {
        document.getElementById('kpi-tx-count').textContent    = kpis.transactions_count;
        document.getElementById('kpi-daily-expense').textContent = formatXOF(kpis.daily_avg_expense);
        const topEl = document.getElementById('kpi-top-category');
        topEl.textContent = kpis.top_expense_category ?? '-';
        topEl.title       = kpis.top_expense_category ?? '';
    }

    function renderAccountCards(accounts, totalBalance) {
        const container = document.getElementById('account-cards');
        container.innerHTML = accounts.map(a => {
            const pct = totalBalance > 0 ? Math.round((a.balance / totalBalance) * 100) : 0;
            return `
                <div class="col-6 col-md-3">
                    <div class="card h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small">${esc(a.name)}</div>
                            <div class="fw-semibold">${formatXOF(a.balance)}</div>
                            <div class="progress mt-1" style="height:4px" title="${pct}% du solde total">
                                <div class="progress-bar" style="width:${pct}%"></div>
                            </div>
                            <div class="text-muted" style="font-size:0.7rem">${pct}% du total</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

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

        const colors = ['#28c98a','#1a2e4a','#7b8fa6','#06b6d4','#3b82f6','#8b5cf6','#f59e0b','#10b981','#ef4444','#64748b'];

        if (chartPie) chartPie.destroy();
        chartPie = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.category_name),
                datasets: [{ data: data.map(d => d.amount), backgroundColor: colors.slice(0, data.length) }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: ctx => ` ${formatXOF(ctx.raw)}` } },
                },
            },
        });
    }

    function renderBarChart(data) {
        const canvas = document.getElementById('chart-monthly-bar');

        if (chartBar) chartBar.destroy();
        chartBar = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.map(d => d.month),
                datasets: [
                    {
                        label: 'Revenus',
                        data: data.map(d => d.income),
                        backgroundColor: 'rgba(40, 201, 138, 0.75)',
                    },
                    {
                        label: 'Dépenses',
                        data: data.map(d => d.expense),
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    },
                ],
            },
            options: {
                plugins: {
                    tooltip: { callbacks: { label: ctx => ` ${formatXOF(ctx.raw)}` } },
                },
                scales: {
                    y: { ticks: { callback: v => formatXOF(v) } },
                },
            },
        });
    }

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
            const isIncome  = tx.sense === 'income';
            const badgeClass  = isIncome ? 'badge-income' : 'badge-expense';
            const amountClass = isIncome ? 'amount-income' : 'amount-expense';
            const prefix      = isIncome ? '+' : '−';
            return `<tr>
                <td>${esc(formatDate(tx.transaction_date))}</td>
                <td>${tx.note ? esc(tx.note) : '<span class="text-muted">-</span>'}</td>
                <td><span class="badge ${badgeClass}">${esc(tx.category?.name ?? '-')}</span></td>
                <td>${esc(tx.account?.name ?? '-')}</td>
                <td class="text-end ${amountClass}">${prefix} ${formatXOF(tx.amount)}</td>
            </tr>`;
        }).join('');
    }

    async function loadDashboard(startDate, endDate) {
        try {
            const [{ data }, monthlyRes] = await Promise.all([
                api.get('/dashboard', { start_date: startDate, end_date: endDate }),
                api.get('/dashboard/monthly'),
            ]);

            // KPIs
            document.getElementById('total-balance').textContent = formatXOF(data.balances.total);
            renderKpis(data.kpis);

            // Cartes comptes
            renderAccountCards(data.balances.accounts, data.balances.total);

            // Résumé période
            const net   = data.period.net;
            const netEl = document.getElementById('period-net');
            document.getElementById('period-income').textContent  = formatXOF(data.period.income);
            document.getElementById('period-expense').textContent = formatXOF(data.period.expense);
            netEl.textContent = formatXOF(net);
            netEl.className   = 'h5 ' + (net >= 0 ? 'amount-income' : 'amount-expense');

            // Graphiques
            renderPieChart(data.expense_by_category);
            renderBarChart(monthlyRes.data);

            // Transactions récentes
            renderRecentTransactions(data.recent_transactions);
        } catch (err) {
            showError('Erreur lors du chargement du dashboard : ' + err.message);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        let [startDate, endDate] = getPeriodDates('month');

        document.getElementById('start-date').value = startDate;
        document.getElementById('end-date').value   = endDate;

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
