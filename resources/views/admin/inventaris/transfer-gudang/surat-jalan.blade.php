<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Jalan - {{ $permintaan->nomor_surat_jalan ?? $permintaan->no_permintaan }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
    .page { max-width: 800px; margin: 0 auto; padding: 20px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 16px; }
    .company-name { font-size: 18px; font-weight: bold; }
    .doc-title { text-align: right; }
    .doc-title h2 { font-size: 16px; font-weight: bold; text-transform: uppercase; }
    .doc-title .nomor { font-size: 13px; color: #555; margin-top: 3px; }

    /* Status badge */
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    .status-menunggu { background: #fef3c7; color: #92400e; border: 1px solid #d97706; }
    .status-disetujui { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .status-ditolak   { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }

    /* Info section */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .info-box { border: 1px solid #ddd; border-radius: 6px; padding: 10px; }
    .info-box .label { font-size: 10px; text-transform: uppercase; color: #888; margin-bottom: 4px; }
    .info-box .value { font-size: 13px; font-weight: bold; }

    /* Meta info */
    .meta-row { display: flex; gap: 24px; margin-bottom: 16px; font-size: 11px; color: #555; }
    .meta-item { display: flex; gap: 6px; align-items: center; }
    .meta-label { font-weight: bold; color: #333; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #374151; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
    tr:last-child td { border-bottom: none; }
    tr:nth-child(even) td { background: #f9fafb; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* Signature area */
    .signature-area { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 30px; }
    .signature-box { text-align: center; }
    .signature-box .title { font-size: 11px; font-weight: bold; margin-bottom: 60px; }
    .signature-box .name-line { border-top: 1px solid #333; padding-top: 5px; font-size: 11px; }

    /* Footer */
    .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee; font-size: 10px; color: #888; text-align: center; }

    @media print {
      body { margin: 0; }
      .page { padding: 10px; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>
<div class="page">

  {{-- Print/Tutup buttons --}}
  <div class="no-print" style="margin-bottom:12px; display:flex; gap:8px;">
    <button onclick="window.print()"
            style="padding:6px 16px; background:#374151; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:12px;">
      🖨 Print / Simpan PDF
    </button>
    <button onclick="window.close()"
            style="padding:6px 16px; background:#f3f4f6; color:#374151; border:1px solid #d1d5db; border-radius:6px; cursor:pointer; font-size:12px;">
      Tutup
    </button>
  </div>

  {{-- Header --}}
  <div class="header">
    <div>
      <div class="company-name">{{ $companyName ?? config('app.name', 'HM Tour & Travel') }}</div>
      <div style="font-size:11px; color:#666; margin-top:3px;">Sistem Manajemen Stok</div>
    </div>
    <div class="doc-title">
      <h2>Surat Jalan</h2>
      <div class="nomor">No: {{ $permintaan->nomor_surat_jalan ?? $permintaan->no_permintaan }}</div>
      <div style="margin-top:6px;">
        <span class="status-badge status-{{ $permintaan->status }}">
          {{ strtoupper($permintaan->status) }}
        </span>
      </div>
    </div>
  </div>

  {{-- Info Pengiriman --}}
  <div class="info-grid">
    <div class="info-box">
      <div class="label">Outlet Pengirim (Asal)</div>
      <div class="value">{{ $permintaan->outletAsal->nama_outlet ?? '-' }}</div>
      @if($permintaan->outletAsal->kode_outlet ?? null)
        <div style="font-size:11px; color:#888; margin-top:2px;">Kode: {{ $permintaan->outletAsal->kode_outlet }}</div>
      @endif
    </div>
    <div class="info-box">
      <div class="label">Outlet Penerima (Tujuan)</div>
      <div class="value">{{ $permintaan->outletTujuan->nama_outlet ?? '-' }}</div>
      @if($permintaan->outletTujuan->kode_outlet ?? null)
        <div style="font-size:11px; color:#888; margin-top:2px;">Kode: {{ $permintaan->outletTujuan->kode_outlet }}</div>
      @endif
    </div>
  </div>

  {{-- Meta --}}
  <div class="meta-row">
    <div class="meta-item">
      <span class="meta-label">Tanggal Permintaan:</span>
      <span>{{ $permintaan->tanggal ? \Carbon\Carbon::parse($permintaan->tanggal)->format('d/m/Y') : ($permintaan->created_at ? $permintaan->created_at->format('d/m/Y') : '-') }}</span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Tanggal Cetak:</span>
      <span>{{ now()->format('d/m/Y H:i') }}</span>
    </div>
    @if($permintaan->status === 'disetujui')
    <div class="meta-item">
      <span class="meta-label">Tanggal Disetujui:</span>
      <span>{{ $permintaan->updated_at ? $permintaan->updated_at->format('d/m/Y H:i') : '-' }}</span>
    </div>
    @endif
  </div>

  {{-- Tabel Barang --}}
  <table>
    <thead>
      <tr>
        <th class="text-center" style="width:40px;">No</th>
        <th>Nama Barang</th>
        <th class="text-center" style="width:80px;">Jenis</th>
        <th class="text-center" style="width:80px;">Jumlah</th>
        <th style="width:120px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @php
        $itemName = '-';
        $itemType = '-';
        if ($permintaan->id_produk) {
            $itemName = $permintaan->produk->nama_produk ?? $permintaan->nama_produk ?? '-';
            $itemType = 'Produk';
        } elseif ($permintaan->id_bahan) {
            $itemName = $permintaan->bahan->nama_bahan ?? $permintaan->nama_bahan ?? '-';
            $itemType = 'Bahan';
        } elseif ($permintaan->id_inventori) {
            $itemName = $permintaan->inventori->nama_barang ?? $permintaan->nama_barang ?? '-';
            $itemType = 'Inventori';
        }
      @endphp
      <tr>
        <td class="text-center">1</td>
        <td style="font-weight:bold;">{{ $itemName }}</td>
        <td class="text-center">{{ $itemType }}</td>
        <td class="text-center">{{ $permintaan->jumlah }}</td>
        <td>{{ $permintaan->status === 'disetujui' ? 'Sudah diterima' : ($permintaan->status === 'ditolak' ? 'Ditolak' : 'Dalam pengiriman') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- Status info --}}
  @if($permintaan->status === 'disetujui')
  <div style="padding:10px; background:#d1fae5; border:1px solid #10b981; border-radius:6px; font-size:12px; color:#065f46; margin-bottom:20px;">
    ✅ Transfer stok telah disetujui dan barang sudah berpindah ke outlet tujuan.
  </div>
  @elseif($permintaan->status === 'ditolak')
  <div style="padding:10px; background:#fee2e2; border:1px solid #ef4444; border-radius:6px; font-size:12px; color:#991b1b; margin-bottom:20px;">
    ❌ Permintaan transfer ini telah ditolak.
  </div>
  @else
  <div style="padding:10px; background:#fef3c7; border:1px solid #d97706; border-radius:6px; font-size:12px; color:#92400e; margin-bottom:20px;">
    ⏳ Menunggu persetujuan dari pihak yang berwenang.
  </div>
  @endif

  {{-- Tanda Tangan --}}
  <div class="signature-area">
    <div class="signature-box">
      <div class="title">Disiapkan oleh<br><small style="font-weight:normal;">(Outlet Pengirim)</small></div>
      <div class="name-line">( ________________________ )</div>
    </div>
    <div class="signature-box">
      <div class="title">Disetujui oleh<br><small style="font-weight:normal;">(Manajemen)</small></div>
      <div class="name-line">( ________________________ )</div>
    </div>
    <div class="signature-box">
      <div class="title">Diterima oleh<br><small style="font-weight:normal;">(Outlet Penerima)</small></div>
      <div class="name-line">( ________________________ )</div>
    </div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    Dicetak pada {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
    {{ $permintaan->nomor_surat_jalan ?? $permintaan->no_permintaan }} &nbsp;|&nbsp;
    Dokumen ini sah tanpa tanda tangan basah jika dicetak dari sistem.
  </div>

</div>
</body>
</html>
