<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Dashboard Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 5px;
            background-color: #f3f4f6;
            border-left: 3px solid #3b82f6;
        }
        .kpi-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .kpi-row {
            display: table-row;
        }
        .kpi-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .kpi-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #111;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table th {
            background-color: #f3f4f6;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }
        .table td {
            padding: 5px 6px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-bold {
            font-weight: bold;
        }
        .summary-box {
            padding: 8px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .summary-label {
            display: table-cell;
            width: 60%;
            font-size: 9px;
        }
        .summary-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-weight: bold;
            font-size: 9px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FINANCE DASHBOARD REPORT</h1>
        <p>{{ $outlet ? $outlet->nama_outlet : 'Semua Outlet' }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
        <p>Dicetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    {{-- KPI Section --}}
    <div class="section">
        <div class="section-title">KEY PERFORMANCE INDICATORS</div>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-cell">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value text-success">Rp {{ number_format($kpi['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Total Expense</div>
                    <div class="kpi-value text-danger">Rp {{ number_format($kpi['total_expense'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Net Profit</div>
                    <div class="kpi-value {{ ($kpi['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($kpi['net_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Profit Margin</div>
                    <div class="kpi-value">{{ number_format($kpi['profit_margin'] ?? 0, 1) }}%</div>
                </div>
            </div>
            <div class="kpi-row">
                <div class="kpi-cell">
                    <div class="kpi-label">Kas & Bank</div>
                    <div class="kpi-value">Rp {{ number_format($kpi['cash_bank_balance'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Total Piutang</div>
                    <div class="kpi-value text-danger">Rp {{ number_format($kpi['total_piutang'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Total Hutang</div>
                    <div class="kpi-value text-danger">Rp {{ number_format($kpi['total_hutang'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Working Capital</div>
                    <div class="kpi-value">Rp {{ number_format($kpi['working_capital'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Horizontal Bar Chart --}}
    <div class="section">
        <div class="section-title">PERBANDINGAN REVENUE, EXPENSE & PROFIT</div>
        @php
            $maxValue = max(
                abs($kpi['total_revenue'] ?? 0),
                abs($kpi['total_expense'] ?? 0),
                abs($kpi['net_profit'] ?? 0)
            );
            
            $revenueWidth = $maxValue > 0 ? (abs($kpi['total_revenue'] ?? 0) / $maxValue) * 100 : 0;
            $expenseWidth = $maxValue > 0 ? (abs($kpi['total_expense'] ?? 0) / $maxValue) * 100 : 0;
            $profitWidth = $maxValue > 0 ? (abs($kpi['net_profit'] ?? 0) / $maxValue) * 100 : 0;
        @endphp
        
        <div style="margin-bottom: 15px;">
            {{-- Revenue Bar --}}
            <div style="margin-bottom: 12px;">
                <div style="display: table; width: 100%; margin-bottom: 3px;">
                    <div style="display: table-cell; width: 30%; font-size: 9px; font-weight: bold;">Revenue</div>
                    <div style="display: table-cell; width: 70%; text-align: right; font-size: 9px; font-weight: bold; color: #10b981;">
                        Rp {{ number_format($kpi['total_revenue'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 100%; height: 20px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $revenueWidth }}%; height: 100%; background: linear-gradient(to right, #10b981, #34d399); display: flex; align-items: center; justify-content: flex-end; padding-right: 5px;">
                        @if($revenueWidth > 15)
                        <span style="color: white; font-size: 8px; font-weight: bold;">{{ number_format($revenueWidth, 0) }}%</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Expense Bar --}}
            <div style="margin-bottom: 12px;">
                <div style="display: table; width: 100%; margin-bottom: 3px;">
                    <div style="display: table-cell; width: 30%; font-size: 9px; font-weight: bold;">Expense</div>
                    <div style="display: table-cell; width: 70%; text-align: right; font-size: 9px; font-weight: bold; color: #ef4444;">
                        Rp {{ number_format($kpi['total_expense'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 100%; height: 20px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $expenseWidth }}%; height: 100%; background: linear-gradient(to right, #ef4444, #f87171); display: flex; align-items: center; justify-content: flex-end; padding-right: 5px;">
                        @if($expenseWidth > 15)
                        <span style="color: white; font-size: 8px; font-weight: bold;">{{ number_format($expenseWidth, 0) }}%</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Profit Bar --}}
            <div style="margin-bottom: 12px;">
                <div style="display: table; width: 100%; margin-bottom: 3px;">
                    <div style="display: table-cell; width: 30%; font-size: 9px; font-weight: bold;">Net Profit</div>
                    <div style="display: table-cell; width: 70%; text-align: right; font-size: 9px; font-weight: bold; color: {{ ($kpi['net_profit'] ?? 0) >= 0 ? '#6366f1' : '#f59e0b' }};">
                        Rp {{ number_format($kpi['net_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 100%; height: 20px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $profitWidth }}%; height: 100%; background: {{ ($kpi['net_profit'] ?? 0) >= 0 ? 'linear-gradient(to right, #6366f1, #818cf8)' : 'linear-gradient(to right, #f59e0b, #fbbf24)' }}; display: flex; align-items: center; justify-content: flex-end; padding-right: 5px;">
                        @if($profitWidth > 15)
                        <span style="color: white; font-size: 8px; font-weight: bold;">{{ number_format($profitWidth, 0) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profit Loss Section --}}
    <div class="section">
        <div class="section-title">LAPORAN LABA RUGI</div>
        <div class="summary-box">
            <div class="summary-row">
                <div class="summary-label">Pendapatan</div>
                <div class="summary-value text-success">Rp {{ number_format($profit_loss_summary['revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Harga Pokok Penjualan (HPP)</div>
                <div class="summary-value text-danger">Rp {{ number_format($profit_loss_summary['cogs'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row" style="border-top: 1px solid #d1d5db; padding-top: 3px; margin-top: 3px;">
                <div class="summary-label text-bold">Laba Kotor</div>
                <div class="summary-value">Rp {{ number_format($profit_loss_summary['gross_profit'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Biaya Operasional</div>
                <div class="summary-value text-danger">Rp {{ number_format($profit_loss_summary['operating_expense'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row" style="border-top: 1px solid #d1d5db; padding-top: 3px; margin-top: 3px;">
                <div class="summary-label text-bold">Laba Operasional</div>
                <div class="summary-value">Rp {{ number_format($profit_loss_summary['operating_profit'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Pendapatan Lain-lain</div>
                <div class="summary-value text-success">Rp {{ number_format($profit_loss_summary['other_revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Biaya Lain-lain</div>
                <div class="summary-value text-danger">Rp {{ number_format($profit_loss_summary['other_expense'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row" style="border-top: 2px solid #333; padding-top: 3px; margin-top: 3px;">
                <div class="summary-label text-bold">LABA BERSIH</div>
                <div class="summary-value {{ ($profit_loss_summary['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($profit_loss_summary['net_profit'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Cashflow Section --}}
    <div class="section">
        <div class="section-title">ARUS KAS</div>
        <div class="summary-box">
            <div class="summary-row text-bold" style="background-color: #dbeafe; padding: 3px;">
                <div class="summary-label">Aktivitas Operasional</div>
                <div class="summary-value"></div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Masuk</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['operating']['inflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Keluar</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['operating']['outflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row text-bold">
                <div class="summary-label" style="padding-left: 10px;">Net Operasional</div>
                <div class="summary-value {{ ($cashflow_summary['operating']['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($cashflow_summary['operating']['net'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="summary-row text-bold" style="background-color: #dbeafe; padding: 3px; margin-top: 5px;">
                <div class="summary-label">Aktivitas Investasi</div>
                <div class="summary-value"></div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Masuk</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['investing']['inflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Keluar</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['investing']['outflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row text-bold">
                <div class="summary-label" style="padding-left: 10px;">Net Investasi</div>
                <div class="summary-value {{ ($cashflow_summary['investing']['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($cashflow_summary['investing']['net'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="summary-row text-bold" style="background-color: #dbeafe; padding: 3px; margin-top: 5px;">
                <div class="summary-label">Aktivitas Pendanaan</div>
                <div class="summary-value"></div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Masuk</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['financing']['inflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kas Keluar</div>
                <div class="summary-value">Rp {{ number_format($cashflow_summary['financing']['outflow'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row text-bold">
                <div class="summary-label" style="padding-left: 10px;">Net Pendanaan</div>
                <div class="summary-value {{ ($cashflow_summary['financing']['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($cashflow_summary['financing']['net'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="summary-row text-bold" style="border-top: 2px solid #333; padding-top: 3px; margin-top: 5px;">
                <div class="summary-label">NET CASHFLOW</div>
                <div class="summary-value {{ ($cashflow_summary['net_cashflow'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($cashflow_summary['net_cashflow'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- Balance Sheet Section --}}
    <div class="section">
        <div class="section-title">NERACA</div>
        <div class="summary-box">
            <div class="summary-row text-bold" style="background-color: #dbeafe; padding: 3px;">
                <div class="summary-label">ASET</div>
                <div class="summary-value"></div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Aset Lancar</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['assets']['current'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Aset Tetap</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['assets']['fixed'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row text-bold">
                <div class="summary-label">Total Aset</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['assets']['total'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row text-bold" style="background-color: #fee2e2; padding: 3px; margin-top: 5px;">
                <div class="summary-label">KEWAJIBAN</div>
                <div class="summary-value"></div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kewajiban Lancar</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['liabilities']['current'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label" style="padding-left: 10px;">Kewajiban Jangka Panjang</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['liabilities']['long_term'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row text-bold">
                <div class="summary-label">Total Kewajiban</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['liabilities']['total'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row text-bold" style="background-color: #d1fae5; padding: 3px; margin-top: 5px;">
                <div class="summary-label">EKUITAS</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['equity'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row text-bold" style="border-top: 2px solid #333; padding-top: 3px; margin-top: 5px;">
                <div class="summary-label">TOTAL KEWAJIBAN + EKUITAS</div>
                <div class="summary-value">Rp {{ number_format($balance_sheet_summary['total_liabilities_equity'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div style="margin-top: 10px;">
            <table class="table">
                <tr>
                    <th>Rasio Keuangan</th>
                    <th class="text-right">Nilai</th>
                </tr>
                <tr>
                    <td>Current Ratio</td>
                    <td class="text-right">{{ number_format($balance_sheet_summary['current_ratio'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Debt to Equity Ratio</td>
                    <td class="text-right">{{ number_format($balance_sheet_summary['debt_to_equity'] ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Piutang & Hutang Aging --}}
    <div class="section">
        <div class="section-title">PIUTANG & HUTANG AGING</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="text-right">Current (0-30)</th>
                    <th class="text-right">31-60 Hari</th>
                    <th class="text-right">61-90 Hari</th>
                    <th class="text-right">> 90 Hari</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-bold">Piutang</td>
                    <td class="text-right">Rp {{ number_format($piutang_aging['current'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutang_aging['overdue_30'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutang_aging['overdue_60'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutang_aging['overdue_90'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right text-bold">Rp {{ number_format(($piutang_aging['current'] ?? 0) + ($piutang_aging['overdue_30'] ?? 0) + ($piutang_aging['overdue_60'] ?? 0) + ($piutang_aging['overdue_90'] ?? 0), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-bold">Hutang</td>
                    <td class="text-right">Rp {{ number_format($hutang_aging['current'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hutang_aging['overdue_30'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hutang_aging['overdue_60'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hutang_aging['overdue_90'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right text-bold">Rp {{ number_format(($hutang_aging['current'] ?? 0) + ($hutang_aging['overdue_30'] ?? 0) + ($hutang_aging['overdue_60'] ?? 0) + ($hutang_aging['overdue_90'] ?? 0), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Monthly Trend --}}
    @if(count($monthly_trend) > 0)
    <div class="section">
        <div class="section-title">TREN BULANAN</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Expense</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_trend as $trend)
                <tr>
                    <td>{{ $trend['month'] }}</td>
                    <td class="text-right">Rp {{ number_format($trend['revenue'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($trend['expense'], 0, ',', '.') }}</td>
                    <td class="text-right {{ $trend['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($trend['profit'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Finance Dashboard Report - Generated by ERP System</p>
    </div>
</body>
</html>
