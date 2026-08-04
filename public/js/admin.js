(() => {
    const charts = {};

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    const pct = v => `${(Number(v) * 100).toFixed(1)} %`;

    function getPeriodDates(period) {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

        if (period === 'week') {
            const from = new Date(now); from.setDate(now.getDate() - 6);
            return [fmt(from), fmt(now)];
        }
        if (period === 'year') {
            return [`${now.getFullYear()}-01-01`, `${now.getFullYear()}-12-31`];
        }
        const from = new Date(now); from.setDate(now.getDate() - 29);
        return [fmt(from), fmt(now)];
    }

    function drawLine(id, labels, data, label, color) {
        const canvas = document.getElementById(id);
        if (charts[id]) charts[id].destroy();
        charts[id] = new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data,
                    borderColor: color,
                    backgroundColor: color + '33',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    function renderOverview(o) {
        document.getElementById('kpi-total-users').textContent  = o.total_users;
        document.getElementById('kpi-new-users').textContent    = o.new_users;
        document.getElementById('kpi-active-users').textContent = o.active_users.month;
        document.getElementById('kpi-operations').textContent   = o.operations;
        document.getElementById('kpi-consent-rate').textContent = pct(o.consent.rate);
    }

    function renderUserGrowth(rows) {
        drawLine('chart-user-growth', rows.map(r => r.month), rows.map(r => r.count), 'Inscriptions', '#28c98a');
    }

    function renderTraffic(rows) {
        drawLine('chart-traffic', rows.map(r => r.date), rows.map(r => r.visits), 'Visites', '#3b82f6');
    }

    function renderActiveUsers(rows) {
        drawLine('chart-active-users', rows.map(r => r.date), rows.map(r => r.active_users), 'Actifs', '#8b5cf6');
    }

    function renderOperations(rows) {
        const canvas = document.getElementById('chart-operations');
        if (charts.operations) charts.operations.destroy();
        charts.operations = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: rows.map(r => r.type),
                datasets: [{
                    label: 'Opérations',
                    data: rows.map(r => r.count),
                    backgroundColor: 'rgba(26, 46, 74, 0.75)',
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    function renderTopFeatures(rows) {
        const canvas = document.getElementById('chart-top-features');
        const noData = document.getElementById('no-features');

        if (!rows.length) {
            canvas.classList.add('d-none');
            noData.classList.remove('d-none');
            if (charts.features) { charts.features.destroy(); charts.features = null; }
            return;
        }
        canvas.classList.remove('d-none');
        noData.classList.add('d-none');

        if (charts.features) charts.features.destroy();
        charts.features = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: rows.map(r => r.feature),
                datasets: [{
                    label: 'Utilisations',
                    data: rows.map(r => r.count),
                    backgroundColor: 'rgba(6, 182, 212, 0.75)',
                }],
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    function renderPerformance(perf) {
        document.getElementById('perf-p95').textContent        = perf.p95_ms;
        document.getElementById('perf-error-rate').textContent = pct(perf.error_rate);
        document.getElementById('perf-total').textContent      = perf.total_requests;

        const tbody = document.getElementById('perf-body');
        const table = document.getElementById('perf-table');
        const noData = document.getElementById('no-perf');

        if (!perf.per_feature.length) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }
        noData.classList.add('d-none');
        table.classList.remove('d-none');

        tbody.innerHTML = perf.per_feature.map(f => `<tr>
            <td>${esc(f.feature)}</td>
            <td class="text-end">${f.count}</td>
            <td class="text-end">${f.avg_ms}</td>
            <td class="text-end">${f.max_ms}</td>
        </tr>`).join('');
    }

    async function loadAdmin(startDate, endDate) {
        const params = { start_date: startDate, end_date: endDate };
        try {
            const [overview, growth, traffic, active, operations, features, perf] = await Promise.all([
                api.get('/admin/metrics/overview', params),
                api.get('/admin/metrics/user-growth'),
                api.get('/admin/metrics/traffic', params),
                api.get('/admin/metrics/active-users', params),
                api.get('/admin/metrics/operations', params),
                api.get('/admin/metrics/top-features', params),
                api.get('/admin/metrics/performance', params),
            ]);

            renderOverview(overview.data);
            renderUserGrowth(growth.data);
            renderTraffic(traffic.data);
            renderActiveUsers(active.data);
            renderOperations(operations.data);
            renderTopFeatures(features.data);
            renderPerformance(perf.data);
        } catch (err) {
            showError('Erreur lors du chargement des statistiques admin : ' + err.message);
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
                loadAdmin(startDate, endDate);
            });
        });

        document.getElementById('apply-period').addEventListener('click', () => {
            const s = document.getElementById('start-date').value;
            const e = document.getElementById('end-date').value;
            if (s && e) {
                document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                loadAdmin(s, e);
            }
        });

        loadAdmin(startDate, endDate);
    });
})();
