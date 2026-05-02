<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SPT Tahunan - {{ $report->taxpayer_name }} - {{ $report->report_year }}</title>
    <style>
        /* Gaya Resmi DJP */
        body { 
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.bordered, table.bordered th, table.bordered td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            vertical-align: top;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .signature-area {
            margin-top: 50px;
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid black;
            width: 300px;
            margin: 30px auto 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            font-size: 8pt;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Halaman 1: Identitas dan Penghasilan -->
    <div class="header">
        <div class="title">SURAT PEMBERITAHUAN TAHUNAN PAJAK PENGHASILAN</div>
        <div class="subtitle">TAHUN PAJAK {{ $report->report_year }}</div>
        <div>FORMULIR 1770{{ in_array('specific_gross_turnover', $tax_objects) ? 'S' : '' }}</div>
    </div>

    <!-- Identitas WP -->
    <table class="bordered">
        <tr>
            <th colspan="4">A. IDENTITAS WAJIB PAJAK</th>
        </tr>
        <tr>
            <td width="25%">1. NPWP</td>
            <td width="25%">{{ $report->npwp }}</td>
            <td width="25%">2. Nama Wajib Pajak</td>
            <td width="25%">{{ $report->taxpayer_name }}</td>
        </tr>
        <tr>
            <td>3. Status PTKP</td>
            <td>{{ $ptkp_label }}</td>
            <td>4. Status Perpajakan</td>
            <td>{{ $marital_status_label }}</td>
        </tr>
        <tr>
            <td>5. Alamat</td>
            <td colspan="3">{{ $report->head_office_country ?? '-' }}</td>
        </tr>
        <tr>
            <td>6. Bidang Usaha</td>
            <td>{{ $report->business_field }}</td>
            <td>7. KLU</td>
            <td>{{ $report->klu_code }}</td>
        </tr>
        <tr>
            <td>8. Jenis Usaha</td>
            <td colspan="3">{{ $report->business_type_label }}</td>
        </tr>
    </table>

    <!-- Di bagian Objek Pajak -->
    <table class="bordered">
        <tr>
            <th colspan="2">B. OBJEK PAJAK</th>
        </tr>
        @if(isset($tax_objects) && is_array($tax_objects))
            @foreach($tax_objects as $object)
            <tr>
                <td width="5%">{{ $loop->iteration }}.</td>
                <td width="95%">
                    @switch($object)
                        @case('final') PPh bersifat final @break
                        @case('specific_gross_turnover') Peredaran bruto tertentu @break
                        @case('general_article17') Tarif umum pasal 17 UU PPh @break
                        @default {{ $object }}
                    @endswitch
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="2">Tidak ada objek pajak yang dipilih</td>
            </tr>
        @endif
    </table>

    <!-- Lampiran Penghasilan -->
    <table class="bordered">
        <tr>
            <th colspan="5">C. PENGHASILAN DAN BIAYA TAHUN {{ $report->report_year }}</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="45%">Uraian</th>
            <th width="15%">Jumlah (Rp)</th>
            <th width="15%">PPh (Rp)</th>
            <th width="20%">Keterangan</th>
        </tr>
        <tr>
            <td class="text-center">1</td>
            <td>Penghasilan Usaha</td>
            <td class="text-right">{{ formatRupiah($report->gross_income ?? 0) }}</td>
            <td class="text-right">{{ formatRupiah($report->tax_withheld ?? 0) }}</td>
            <td>-</td>
        </tr>
        <!-- Tambahkan baris lainnya sesuai kebutuhan -->
    </table>

    <!-- Halaman 2: Perhitungan PPh -->
    <div class="page-break"></div>
    
    <div class="header">
        <div class="title">PERHITUNGAN PAJAK PENGHASILAN</div>
        <div class="subtitle">TAHUN PAJAK {{ $report->report_year }}</div>
    </div>

    <!-- Perhitungan PKP -->
    <table class="bordered">
        <tr>
            <th colspan="3">1. PENGHITUNGAN PENGHASILAN KENA PAJAK</th>
        </tr>
        <tr>
            <td width="60%">a. Penghasilan Neto</td>
            <td width="20%" class="text-right">Rp</td>
            <td width="20%" class="text-right">{{ formatRupiah($report->net_income ?? 0) }}</td>
        </tr>
        <tr>
            <td>b. PTKP ({{ $ptkp_label }})</td>
            <td class="text-right">Rp</td>
            <td class="text-right">({{ formatRupiah($report->ptkp_value ?? 0) }})</td>
        </tr>
        <tr class="text-bold">
            <td>c. Penghasilan Kena Pajak</td>
            <td class="text-right">Rp</td>
            <td class="text-right">{{ formatRupiah(max(0, ($report->net_income ?? 0) - ($report->ptkp_value ?? 0))) }}</td>
        </tr>
    </table>

    <!-- Tarif Pajak -->
    <table class="bordered">
        <tr>
            <th colspan="4">2. PENGHITUNGAN PAJAK TERUTANG</th>
        </tr>
        <tr>
            <th width="40%">Lapisan Penghasilan</th>
            <th width="15%">Tarif</th>
            <th width="25%">Pajak (Rp)</th>
            <th width="20%">Keterangan</th>
        </tr>
        @php
            $pkp = max(0, ($report->net_income ?? 0) - ($report->ptkp_value ?? 0));
            $totalTax = 0;
        @endphp
        
        @foreach($tax_rates as $rate)
        @php
            $lower = $rate['lower_limit'];
            $upper = $rate['upper_limit'] ?? PHP_FLOAT_MAX;
            $ratePercent = $rate['rate'];
            
            if ($pkp <= 0) {
                $taxAmount = 0;
            } elseif ($pkp <= $lower) {
                $taxAmount = 0;
            } else {
                $taxable = min($pkp, $upper) - $lower;
                $taxAmount = $taxable * ($ratePercent / 100);
                $totalTax += $taxAmount;
            }
        @endphp
        <tr>
            <td>
                @if($rate['upper_limit'])
                    {{ formatRupiah($rate['lower_limit']) }} - {{ formatRupiah($rate['upper_limit']) }}
                @else
                    > {{ formatRupiah($rate['lower_limit']) }}
                @endif
            </td>
            <td class="text-center">{{ $rate['rate'] }}%</td>
            <td class="text-right">{{ formatRupiah($taxAmount) }}</td>
            <td class="text-center">-</td>
        </tr>
        @endforeach
        <tr class="text-bold">
            <td colspan="2">Total Pajak Terutang</td>
            <td class="text-right">{{ formatRupiah($totalTax) }}</td>
            <td></td>
        </tr>
    </table>

    <!-- Informasi Lain -->
    <table class="bordered">
        <tr>
            <th colspan="2">3. INFORMASI LAIN</th>
        </tr>
        <tr>
            <td width="30%">Pembukuan Diaudit</td>
            <td width="70%">{{ $report->is_audited ? 'Ya' : 'Tidak' }}</td>
        </tr>
        @if($report->is_audited)
        <tr>
            <td>Opini Audit</td>
            <td>
                @switch($report->audit_opinion)
                    @case('unqualified') Wajar Tanpa Pengecualian @break
                    @case('qualified') Wajar Dengan Pengecualian @break
                    @case('adverse') Tidak Wajar @break
                    @case('no_opinion') Tidak Ada Opini @break
                @endswitch
            </td>
        </tr>
        @endif
        <tr>
            <td>Menggunakan Konsultan Pajak</td>
            <td>{{ $report->uses_tax_consultant ? 'Ya' : 'Tidak' }}</td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature-area">
        <div style="text-align: center; width: 60%; margin: 0 auto;">
            <div>{{ $report->taxpayer_name }}</div>
            <div class="signature-line"></div>
            <div>Nama Jelas & Tanda Tangan</div>
            <div style="margin-top: 20px;">Tanggal: {{ $current_date }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Formulir ini merupakan bagian yang tidak terpisahkan dari SPT Tahunan PPh Tahun {{ $report->report_year }}
    </div>

    <!-- Helper Function -->
    @php
        function formatRupiah($value) {
            return number_format($value ?? 0, 0, ',', '.');
        }
    @endphp
</body>
</html>
