@php
    // Logo embarqué en base64 (dompdf lit le disque, pas les URLs ; le data URI est le plus fiable).
    $logoPath = public_path('images/oeil360-logo-horizontal.png');
    $logo = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

    $s = $report['summary'];
    $netColor = $s['net'] >= 0 ? '#28c98a' : '#ef4444';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Relevé financier - Oeil 360 Finance</title>
    <style>
        @page { margin: 2.1cm 1.5cm 2.3cm 1.5cm; }

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a2e4a;
            margin: 0;
        }

        /* Letterhead */
        .letterhead {
            width: 100%;
            background: #1a2e4a;
            border-bottom: 3px solid #28c98a;
            border-collapse: collapse;
        }
        .letterhead td { padding: 14px 16px; vertical-align: middle; }
        .letterhead img { height: 34px; }
        .letterhead .brand-name { color: #ffffff; font-size: 15px; font-weight: bold; }
        .letterhead .contacts {
            text-align: right; color: #cdd7e5; font-size: 8.5px; line-height: 1.5;
        }
        .letterhead .contacts strong { color: #ffffff; font-size: 10px; }

        /* Document title */
        .doc-title { margin: 18px 0 4px 0; }
        .doc-title h1 { margin: 0; font-size: 19px; color: #1a2e4a; }
        .doc-title .meta { color: #64748b; font-size: 9.5px; margin-top: 3px; }

        h2 {
            font-size: 12px; color: #1a2e4a; margin: 20px 0 8px 0;
            padding-left: 8px; border-left: 3px solid #28c98a;
        }

        /* KPI cards */
        .kpis { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-top: 10px; }
        .kpis td {
            width: 25%; background: #f6f9fc; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 10px 12px; vertical-align: top;
        }
        .kpis .label { color: #64748b; font-size: 8.5px; text-transform: uppercase; letter-spacing: .3px; }
        .kpis .value { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .subline { color: #64748b; font-size: 9px; margin: 10px 0 0 0; }

        /* Data tables */
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data thead { display: table-header-group; }
        table.data th {
            background: #1a2e4a; color: #ffffff; font-size: 9px; font-weight: bold;
            text-align: left; padding: 7px 8px; text-transform: uppercase; letter-spacing: .2px;
        }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; }
        table.data tr { page-break-inside: avoid; }
        table.data tr.even td { background: #f6f9fc; }
        .num { text-align: right; white-space: nowrap; }
        .income { color: #1dac74; font-weight: bold; }
        .expense { color: #ef4444; font-weight: bold; }
        tr.total td { background: #edfdf6; font-weight: bold; border-top: 2px solid #28c98a; }
        .empty { color: #94a3b8; font-style: italic; padding: 8px 2px; font-size: 9.5px; }

        /* Footer (fixed on every page) */
        .footer {
            position: fixed; bottom: -1.5cm; left: 0; right: 0;
            color: #94a3b8; font-size: 8px; line-height: 1.4;
            border-top: 1px solid #e2e8f0; padding-top: 6px;
        }
        .footer .pages { text-align: right; }
        .footer .pages:after { content: "Page " counter(page) " / " counter(pages); }
    </style>
</head>
<body>

    <div class="footer">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="border:0; padding:0;">
                    Oeil360Finance · Porto-Novo, Bénin · oeil360finance@bytechnum.com<br>
                    Document personnel généré à la demande du titulaire (droit d'accès, APDP loi n°2017-20).
                </td>
                <td class="pages" style="border:0; padding:0; vertical-align:bottom;"></td>
            </tr>
        </table>
    </div>

    {{-- Letterhead --}}
    <table class="letterhead">
        <tr>
            <td>
                @if ($logo)
                    <img src="{{ $logo }}" alt="Oeil 360 Finance">
                @else
                    <span class="brand-name">Oeil 360° Finance</span>
                @endif
            </td>
            <td class="contacts">
                <strong>Édité par bytechnum</strong><br>
                oeil360finance@bytechnum.com<br>
                oeil360finance.bytechnum.com · Porto-Novo, Bénin
            </td>
        </tr>
    </table>

    {{-- Titre --}}
    <div class="doc-title">
        <h1>Relevé financier</h1>
        <div class="meta">
            {{ $report['period']['label'] }}
            &nbsp;·&nbsp; Titulaire : {{ $report['user']['name'] }} ({{ $report['user']['email'] }})
            &nbsp;·&nbsp; Généré le {{ $report['generated_at']->format('d/m/Y à H:i') }}
        </div>
    </div>

    {{-- Résumé --}}
    <h2>Résumé</h2>
    <table class="kpis">
        <tr>
            <td>
                <div class="label">Solde total</div>
                <div class="value" style="color:#1a2e4a;">{{ formatXof($s['total_balance']) }}</div>
            </td>
            <td>
                <div class="label">Total entrées</div>
                <div class="value" style="color:#1dac74;">{{ formatXof($s['income']) }}</div>
            </td>
            <td>
                <div class="label">Total sorties</div>
                <div class="value" style="color:#ef4444;">{{ formatXof($s['expense']) }}</div>
            </td>
            <td>
                <div class="label">Solde net (période)</div>
                <div class="value" style="color:{{ $netColor }};">{{ formatXof($s['net']) }}</div>
            </td>
        </tr>
    </table>
    <p class="subline">
        {{ $s['transactions_count'] }} transaction(s) sur la période
        &nbsp;·&nbsp; Dépense moyenne / jour : {{ formatXof($s['daily_avg_expense']) }}
        &nbsp;·&nbsp; Catégorie la plus dépensière : {{ $s['top_expense_category'] ?? '-' }}
    </p>

    {{-- Soldes par compte --}}
    <h2>Soldes par compte</h2>
    @if (count($s['accounts']) > 0)
        <table class="data">
            <thead>
                <tr><th>Compte</th><th>Type</th><th class="num">Solde</th></tr>
            </thead>
            <tbody>
                @foreach ($s['accounts'] as $account)
                    <tr class="{{ $loop->even ? 'even' : '' }}">
                        <td>{{ $account['name'] }}</td>
                        <td>{{ $account['type'] }}</td>
                        <td class="num">{{ formatXof($account['balance']) }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="2">Solde total</td>
                    <td class="num">{{ formatXof($s['total_balance']) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">Aucun compte.</div>
    @endif

    {{-- Transactions --}}
    <h2>Transactions</h2>
    @if (count($report['transactions']) > 0)
        <table class="data">
            <thead>
                <tr>
                    <th>Date</th><th>Catégorie</th><th>Compte</th><th>Sens</th><th>Note</th><th class="num">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['transactions'] as $t)
                    <tr class="{{ $loop->even ? 'even' : '' }}">
                        <td>{{ $t['date'] }}</td>
                        <td>{{ $t['category'] }}</td>
                        <td>{{ $t['account'] }}</td>
                        <td>{{ $t['sense'] }}</td>
                        <td>{{ $t['note'] }}</td>
                        <td class="num {{ $t['sense'] === 'Entrée' ? 'income' : 'expense' }}">
                            {{ $t['sense'] === 'Entrée' ? '+' : '-' }}{{ formatXof($t['amount']) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Aucune transaction sur la période.</div>
    @endif

    {{-- Transferts --}}
    <h2>Transferts</h2>
    @if (count($report['transfers']) > 0)
        <table class="data">
            <thead>
                <tr><th>Date</th><th>De</th><th>Vers</th><th class="num">Montant</th><th>Note</th></tr>
            </thead>
            <tbody>
                @foreach ($report['transfers'] as $t)
                    <tr class="{{ $loop->even ? 'even' : '' }}">
                        <td>{{ $t['date'] }}</td>
                        <td>{{ $t['from'] }}</td>
                        <td>{{ $t['to'] }}</td>
                        <td class="num">{{ formatXof($t['amount']) }}</td>
                        <td>{{ $t['note'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Aucun transfert sur la période.</div>
    @endif

    {{-- Récurrentes --}}
    <h2>Transactions récurrentes</h2>
    @if (count($report['recurring']) > 0)
        <table class="data">
            <thead>
                <tr>
                    <th>Catégorie</th><th>Compte</th><th>Sens</th><th>Fréquence</th>
                    <th class="num">Montant</th><th>Prochaine</th><th>Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['recurring'] as $r)
                    <tr class="{{ $loop->even ? 'even' : '' }}">
                        <td>{{ $r['category'] }}</td>
                        <td>{{ $r['account'] }}</td>
                        <td>{{ $r['sense'] }}</td>
                        <td>{{ $r['frequency'] }}</td>
                        <td class="num">{{ formatXof($r['amount']) }}</td>
                        <td>{{ $r['next'] }}</td>
                        <td>{{ $r['active'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Aucune transaction récurrente.</div>
    @endif

</body>
</html>
