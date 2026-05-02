<style>
    .signature-preview {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
        border: 1px solid #eee;
        background-color: #f9f9f9;
        padding: 5px;
    }
    .feather-icon-lg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }
    .feather-icon-sm {
        width: 16px;
        height: 16px;
        stroke-width: 2;
    }
    .feather-icon-xs {
        width: 14px;
        height: 14px;
        stroke-width: 1.5;
    }
    .table th {
        white-space: nowrap;
        position: relative;
    }
    .table th i {
        vertical-align: middle;
        margin-right: 5px;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>

@extends('app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i data-feather="file-text" class="feather-icon-lg mr-2"></i>
                    <h5 class="mb-0 font-weight-bold">Daftar SPT Tahunan</h5>
                </div>
                <a href="{{ route('financial.annual-tax-report.create') }}" class="btn btn-success btn-sm">
                    <i data-feather="plus" class="feather-icon-sm mr-1"></i> Tambah Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th width="8%" class="text-center">
                                <i data-feather="calendar" class="feather-icon-sm"></i> Tahun
                            </th>
                            <th width="15%">
                                <i data-feather="book" class="feather-icon-sm"></i> Tahun Buku
                            </th>
                            <th width="12%">
                                <i data-feather="credit-card" class="feather-icon-sm"></i> NPWP
                            </th>
                            <th width="20%">
                                <i data-feather="user" class="feather-icon-sm"></i> Nama WP
                            </th>
                            <th width="15%">
                                <i data-feather="briefcase" class="feather-icon-sm"></i> Jenis Usaha
                            </th>
                            <th width="10%" class="text-center">
                                <i data-feather="info" class="feather-icon-sm"></i> Status
                            </th>
                            <th width="20%" class="text-center">
                                <i data-feather="activity" class="feather-icon-sm"></i> Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td class="text-center font-weight-bold">{{ $report->report_year }}</td>
                            <td>{{ $report->accountingBook->name }}</td>
                            <td>{{ $report->npwp }}</td>
                            <td>{{ $report->taxpayer_name }}</td>
                            <td>
                                @php
                                    $businessTypes = [
                                        'service' => 'Jasa',
                                        'perpetual_trade' => 'Dagang Perpetual',
                                        'periodic_trade' => 'Dagang Periodik',
                                        'service_perpetual_trade' => 'Jasa & Dagang Perpetual',
                                        'service_periodic_trade' => 'Jasa & Dagang Periodik'
                                    ];
                                @endphp
                                {{ $businessTypes[$report->business_type] ?? $report->business_type }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $report->is_completed ? 'success' : 'warning' }} py-1 px-2">
                                    {{ $report->is_completed ? 'Lengkap' : 'Draft' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-primary edit-btn" 
                                            data-id="{{ $report->id }}"
                                            data-edit-url="{{ route('financial.annual-tax-report.edit', $report->id) }}"
                                            data-update-url="{{ route('financial.annual-tax-report.update', $report->id) }}"
                                            title="Edit">
                                        <i data-feather="edit" class="feather-icon-xs"></i>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-info show-pdf-btn ml-1"
                                            data-id="{{ $report->id }}"
                                            data-show-url="{{ route('financial.annual-tax-report.show', $report->id) }}"
                                            title="Lihat PDF">
                                        <i data-feather="file" class="feather-icon-xs"></i>
                                    </button>
                                    
                                    <form action="{{ route('financial.annual-tax-report.destroy', $report->id) }}" method="POST" class="d-inline ml-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus SPT ini?')">
                                            <i data-feather="trash-2" class="feather-icon-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit"></i> Edit SPT Tahunan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Data Umum -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">I. Data Umum</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Buku</label>
                                        <select name="accounting_book_id" class="form-control" required>
                                            @foreach($books as $book)
                                            <option value="{{ $book->id }}">{{ $book->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Pajak</label>
                                        <input type="number" name="report_year" class="form-control" 
                                            value="{{ date('Y') }}" min="2000" max="{{ date('Y')+1 }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pilih Objek Pajak -->
                            <div class="form-group">
                                <label>Pilih Objek Pajak (Checkbox)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tax_object[]" 
                                        id="tax_object_final" value="final">
                                    <label class="form-check-label" for="tax_object_final">
                                        PPh bersifat final
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tax_object[]" 
                                        id="tax_object_specific" value="specific_gross_turnover">
                                    <label class="form-check-label" for="tax_object_specific">
                                        Peredaran bruto tertentu
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tax_object[]" 
                                        id="tax_object_general" value="general_article17">
                                    <label class="form-check-label" for="tax_object_general">
                                        Tarif umum pasal 17 UU PPh
                                    </label>
                                </div>
                            </div>
                            
                            <!-- PTKP -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Status PTKP</label>
                                        <select name="ptkp_status" id="ptkpStatus" class="form-control" required>
                                            <option value="">Pilih Status PTKP</option>
                                            <option value="TK/0" data-ptkp-value="54000000">TK/0 - Tidak Kawin/Tanggungan 0 (Rp54.000.000)</option>
                                            <option value="TK/1" data-ptkp-value="58500000">TK/1 - Tidak Kawin/Tanggungan 1 (Rp58.500.000)</option>
                                            <option value="TK/2" data-ptkp-value="63000000">TK/2 - Tidak Kawin/Tanggungan 2 (Rp63.000.000)</option>
                                            <option value="TK/3" data-ptkp-value="67500000">TK/3 - Tidak Kawin/Tanggungan 3 (Rp67.500.000)</option>
                                            <option value="K/0" data-ptkp-value="58500000">K/0 - Kawin/Tanggungan 0 (Rp58.500.000)</option>
                                            <option value="K/1" data-ptkp-value="63000000">K/1 - Kawin/Tanggungan 1 (Rp63.000.000)</option>
                                            <option value="K/2" data-ptkp-value="67500000">K/2 - Kawin/Tanggungan 2 (Rp67.500.000)</option>
                                            <option value="K/3" data-ptkp-value="72000000">K/3 - Kawin/Tanggungan 3 (Rp72.000.000)</option>
                                            <option value="K/I/0" data-ptkp-value="112500000">K/I/0 - Kawin/Istri bekerja/Tanggungan 0 (Rp112.500.000)</option>
                                            <option value="K/I/1" data-ptkp-value="117000000">K/I/1 - Kawin/Istri bekerja/Tanggungan 1 (Rp117.000.000)</option>
                                            <option value="K/I/2" data-ptkp-value="121500000">K/I/2 - Kawin/Istri bekerja/Tanggungan 2 (Rp121.500.000)</option>
                                            <option value="K/I/3" data-ptkp-value="126000000">K/I/3 - Kawin/Istri bekerja/Tanggungan 3 (Rp126.000.000)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Perpajakan Suami Istri -->
                            <div class="form-group">
                                <label>Status Perpajakan Suami Istri</label>
                                <select name="marital_tax_status" class="form-control" required>
                                    <option value="">Pilih Status</option>
                                    <option value="KK">KK - Kewajiban bersama</option>
                                    <option value="HB">HB - Hidup berpisah (putusan hakim)</option>
                                    <option value="PH">PH - Pemisahan harta</option>
                                    <option value="MT">MT - Kewajiban terpisah (isteri memilih)</option>
                                </select>
                            </div>
                            
                            <!-- Tabel Tarif Pajak -->
                            <div class="form-group">
                                <label>Tarif Pajak</label>
                                <table class="table table-bordered" id="taxRateTable">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Batas Bawah (Rp)</th>
                                            <th>< PKP ≤</th>
                                            <th>Batas Atas (Rp)</th>
                                            <th>Tarif (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < 5; $i++)
                                        <tr>
                                            <td>
                                                <select name="tax_rates[{{ $i }}][level]" class="form-control" required>
                                                    <option value="I" {{ $i == 0 ? 'selected' : '' }}>I</option>
                                                    <option value="II" {{ $i == 1 ? 'selected' : '' }}>II</option>
                                                    <option value="III" {{ $i == 2 ? 'selected' : '' }}>III</option>
                                                    <option value="IV" {{ $i == 3 ? 'selected' : '' }}>IV</option>
                                                    <option value="V" {{ $i == 4 ? 'selected' : '' }}>V</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="tax_rates[{{ $i }}][lower_limit]" 
                                                    class="form-control" value="{{ $i == 0 ? 0 : '' }}" required>
                                            </td>
                                            <td>< PKP ≤</td>
                                            <td>
                                                <input type="number" name="tax_rates[{{ $i }}][upper_limit]" 
                                                    class="form-control" {{ $i == 4 ? 'readonly' : '' }} 
                                                    value="{{ $i == 4 ? '' : '' }}">
                                            </td>
                                            <td>
                                                <input type="number" name="tax_rates[{{ $i }}][rate]" 
                                                    class="form-control" step="0.01" min="0" max="100" required>
                                            </td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Identitas WP -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">II. Identitas Wajib Pajak</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NPWP (15 digit, tuliskan tanpa tanda titik dan tanda minus)</label>
                                        <input type="text" name="npwp" class="form-control" 
                                            placeholder="12.345.678.9-012.345" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Wajib Pajak</label>
                                        <input type="text" name="taxpayer_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bidang Usaha</label>
                                        <input type="text" name="business_field" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Usaha</label>
                                        <select name="business_type" class="form-control" required>
                                            <option value="service">Jasa</option>
                                            <option value="perpetual_trade">Dagang Perpetual</option>
                                            <option value="periodic_trade">Dagang Periodik</option>
                                            <option value="service_perpetual_trade">Jasa & Dagang Perpetual</option>
                                            <option value="service_periodic_trade">Jasa & Dagang Periodik</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>KLU</label>
                                        <input type="text" name="klu_code" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>No. Telepon</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Periode Pembukuan</label>
                                        <input type="text" name="accounting_period" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pembetulan SPT ke-</label>
                                        <input type="number" name="revision_number" class="form-control" value="0" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Negara Domisili Kantor Pusat (khusus BUT)</label>
                                        <input type="text" name="head_office_country" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Tanda Tangan Wajib Pajak</label>
                                <input type="file" name="taxpayer_signature" class="form-control-file">
                                <small class="text-muted">Format: JPG, PNG (max 2MB)</small>
                                <!-- Tempat untuk preview gambar tanda tangan -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Identitas SPT -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">III. Identitas SPT</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Pembukuan/Laporan Keuangan</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_audited" 
                                        id="is_audited_yes" value="1">
                                    <label class="form-check-label" for="is_audited_yes">
                                        Diaudit
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_audited" 
                                        id="is_audited_no" value="0" checked>
                                    <label class="form-check-label" for="is_audited_no">
                                        Tidak Diaudit
                                    </label>
                                </div>
                            </div>
                            
                            <div id="auditFields" style="display: none;">
                                <div class="form-group">
                                    <label>Opini Akuntan</label>
                                    <select name="audit_opinion" class="form-control">
                                        <option value="unqualified">Wajar Tanpa Pengecualian</option>
                                        <option value="qualified">Wajar Dengan Pengecualian</option>
                                        <option value="adverse">Tidak Wajar</option>
                                        <option value="no_opinion">Tidak Ada Opini</option>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Kantor Akuntan Publik</label>
                                            <input type="text" name="audit_firm_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NPWP Kantor Akuntan Publik</label>
                                            <input type="text" name="audit_firm_npwp" class="form-control" 
                                                placeholder="12.345.678.9-012.345">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Akuntan Publik</label>
                                            <input type="text" name="auditor_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NPWP Akuntan Publik</label>
                                            <input type="text" name="auditor_npwp" class="form-control" 
                                                placeholder="12.345.678.9-012.345">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Menggunakan Kuasa Konsultan Pajak?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="uses_tax_consultant" 
                                        id="uses_consultant_yes" value="1">
                                    <label class="form-check-label" for="uses_consultant_yes">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="uses_tax_consultant" 
                                        id="uses_consultant_no" value="0" checked>
                                    <label class="form-check-label" for="uses_consultant_no">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            
                            <div id="consultantFields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Konsultan Pajak</label>
                                            <input type="text" name="consultant_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NPWP Konsultan Pajak</label>
                                            <input type="text" name="consultant_npwp" class="form-control" 
                                                placeholder="12.345.678.9-012.345">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Kantor Konsultan Pajak</label>
                                            <input type="text" name="consultant_firm_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NPWP Kantor Konsultan Pajak</label>
                                            <input type="text" name="consultant_firm_npwp" class="form-control" 
                                                placeholder="12.345.678.9-012.345">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Tanda Tangan Konsultan Pajak</label>
                                    <input type="file" name="consultant_signature" class="form-control-file">
                                    <small class="text-muted">Format: JPG, PNG, PDF (max 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lampiran -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">IV. Lampiran</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Kompensasi Kerugian Fiskal (Lampiran 2A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_fiscal_loss_compensation" 
                                        id="has_loss_compensation_yes" value="1">
                                    <label class="form-check-label" for="has_loss_compensation_yes">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_fiscal_loss_compensation" 
                                        id="has_loss_compensation_no" value="0" checked>
                                    <label class="form-check-label" for="has_loss_compensation_no">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Transaksi Hubungan Istimewa (Lampiran 3A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_related_party_transactions" 
                                        id="has_related_transactions_yes" value="1">
                                    <label class="form-check-label" for="has_related_transactions_yes">
                                        Ada transaksi dalam hubungan istimewa dan/atau dengan tax haven country
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_related_party_transactions" 
                                        id="has_related_transactions_no" value="0" checked>
                                    <label class="form-check-label" for="has_related_transactions_no">
                                        Tidak ada transaksi dalam hubungan istimewa dan/atau dengan tax haven country
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Fasilitas Penanaman Modal (Lampiran 4A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_investment_facilities" 
                                        id="has_investment_facilities_yes" value="1">
                                    <label class="form-check-label" for="has_investment_facilities_yes">
                                        Ada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_investment_facilities" 
                                        id="has_investment_facilities_no" value="0" checked>
                                    <label class="form-check-label" for="has_investment_facilities_no">
                                        Tidak Ada
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Cabang Utama Perusahaan (Lampiran 5A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_main_branches" 
                                        id="has_main_branches_yes" value="1">
                                    <label class="form-check-label" for="has_main_branches_yes">
                                        Ada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_main_branches" 
                                        id="has_main_branches_no" value="0" checked>
                                    <label class="form-check-label" for="has_main_branches_no">
                                        Tidak Ada
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Penghasilan dari Luar Negeri (Lampiran 7A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_foreign_income" 
                                        id="has_foreign_income_yes" value="1">
                                    <label class="form-check-label" for="has_foreign_income_yes">
                                        Ada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="has_foreign_income" 
                                        id="has_foreign_income_no" value="0" checked>
                                    <label class="form-check-label" for="has_foreign_income_no">
                                        Tidak Ada
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Form Transkrip Laporan Keuangan (Lampiran 8A)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="financial_statement_type" 
                                        id="financial_statement_8a2" value="8A-2" checked>
                                    <label class="form-check-label" for="financial_statement_8a2">
                                        8A-2 Perusahaan Dagang
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="financial_statement_type" 
                                        id="financial_statement_8a6" value="8A-6">
                                    <label class="form-check-label" for="financial_statement_8a6">
                                        8A-6 Non Kualifikasi
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal untuk preview PDF -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Preview SPT Tahunan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe id="pdfPreviewFrame" class="embed-responsive-item" src=""></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <a id="pdfDownloadBtn" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Unduh PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    feather.replace();
    // Inisialisasi variabel untuk menyimpan data report
    let currentReport = null;
    baseUrl = window.baseUrl;
    
    // Tangani klik tombol edit
    $('.edit-btn').click(function() {
        const reportId = $(this).data('id');
        const editUrl = $(this).data('edit-url');
        const updateUrl = $(this).data('update-url');
        
        // Set action form
        $('#editForm').attr('action', updateUrl);
        
        // Ambil data via AJAX
        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                console.log('Response from server:', response);
                fillEditForm(response);
                $('#editModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error loading edit data:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data SPT'
                });
            }
        });
    });
    
    // Fungsi untuk mengisi form edit dengan data
    function fillEditForm(data) {
        // Data Umum
        $('select[name="accounting_book_id"]').val(data.accounting_book_id);
        $('input[name="report_year"]').val(data.report_year);
        
        // Log data yang diterima
        console.log('Data received for edit:', data);
        
        // 1. Objek Pajak (Checkbox)
        $('input[name="tax_object[]"]').prop('checked', false);
        if (data.tax_object && Array.isArray(data.tax_object)) {
            console.log('Tax object data:', data.tax_object);
            data.tax_object.forEach(function(item) {
                console.log(`Checking checkbox for: ${item}`);
                $(`input[name="tax_object[]"][value="${item}"]`).prop('checked', true);
            });
        } else {
            console.warn('Tax object data is missing or not an array');
        }
        
        // 2. Tarif Pajak
        if (data.tax_rate_data && Array.isArray(data.tax_rate_data)) {
            console.log('Tax rate data:', data.tax_rate_data);
            data.tax_rate_data.forEach((rate, index) => {
                $(`select[name="tax_rates[${index}][level]"]`).val(rate.level);
                $(`input[name="tax_rates[${index}][lower_limit]"]`).val(rate.lower_limit);
                $(`input[name="tax_rates[${index}][upper_limit]"]`).val(rate.upper_limit);
                $(`input[name="tax_rates[${index}][rate]"]`).val(rate.rate);
            });
        } else {
            console.warn('Tax rate data is missing or not an array');
        }
        
        // PTKP
        $('select[name="ptkp_status"]').val(data.ptkp_status).trigger('change');
        $('select[name="marital_tax_status"]').val(data.marital_tax_status);
        
        // Identitas WP
        $('input[name="npwp"]').val(data.npwp);
        $('input[name="taxpayer_name"]').val(data.taxpayer_name);
        $('input[name="business_field"]').val(data.business_field);
        $('select[name="business_type"]').val(data.business_type);
        $('input[name="klu_code"]').val(data.klu_code);
        $('input[name="phone"]').val(data.phone);
        $('input[name="accounting_period"]').val(data.accounting_period);
        $('input[name="revision_number"]').val(data.revision_number);
        $('input[name="head_office_country"]').val(data.head_office_country);
        
        // 3. Tanda Tangan Wajib Pajak (Preview gambar langsung)
        if (data.taxpayer_signature) {
            $('#currentSignature').remove();
            $('input[name="taxpayer_signature"]').closest('.form-group').append(`
                <div id="currentSignature" class="mt-2">
                    <p>Tanda Tangan Saat Ini:</p>
                    <img src="/${data.taxpayer_signature}" 
                        class="img-fluid signature-preview"
                        style="max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSignature()">
                            <i class="fas fa-trash"></i> Hapus Tanda Tangan
                        </button>
                        <input type="hidden" name="remove_signature" value="0">
                    </div>
                </div>
            `);
        }
        
        // Identitas SPT
        $(`input[name="is_audited"][value="${data.is_audited ? 1 : 0}"]`).prop('checked', true).trigger('change');
        $('select[name="audit_opinion"]').val(data.audit_opinion);
        $('input[name="audit_firm_name"]').val(data.audit_firm_name);
        $('input[name="audit_firm_npwp"]').val(data.audit_firm_npwp);
        $('input[name="auditor_name"]').val(data.auditor_name);
        $('input[name="auditor_npwp"]').val(data.auditor_npwp);
        
        $(`input[name="uses_tax_consultant"][value="${data.uses_tax_consultant ? 1 : 0}"]`).prop('checked', true).trigger('change');
        $('input[name="consultant_name"]').val(data.consultant_name);
        $('input[name="consultant_npwp"]').val(data.consultant_npwp);
        $('input[name="consultant_firm_name"]').val(data.consultant_firm_name);
        $('input[name="consultant_firm_npwp"]').val(data.consultant_firm_npwp);
        
        // Lampiran
        $(`input[name="has_fiscal_loss_compensation"][value="${data.has_fiscal_loss_compensation ? 1 : 0}"]`).prop('checked', true);
        $(`input[name="has_related_party_transactions"][value="${data.has_related_party_transactions ? 1 : 0}"]`).prop('checked', true);
        $(`input[name="has_investment_facilities"][value="${data.has_investment_facilities ? 1 : 0}"]`).prop('checked', true);
        $(`input[name="has_main_branches"][value="${data.has_main_branches ? 1 : 0}"]`).prop('checked', true);
        $(`input[name="has_foreign_income"][value="${data.has_foreign_income ? 1 : 0}"]`).prop('checked', true);
        $(`input[name="financial_statement_type"][value="${data.financial_statement_type}"]`).prop('checked', true);
    }

    function removeSignature() {
        $('#currentSignature').remove();
        $('input[name="remove_signature"]').val(1);
        $('input[name="taxpayer_signature"]').val('');
    }
    
    // Fungsi-fungsi dari create yang digunakan kembali
    function formatRupiah(angka) {
        if (!angka) return '';
        const numberString = angka.toString();
        const split = numberString.split('.');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/g);
        
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        return split[1] ? 'Rp' + rupiah + ',' + split[1] : 'Rp' + rupiah;
    }

    function toggleAuditFields() {
        if ($('input[name="is_audited"]:checked').val() == '1') {
            $('#auditFields').show();
            $('#auditFields select, #auditFields input').prop('required', true);
        } else {
            $('#auditFields').hide();
            $('#auditFields select, #auditFields input').prop('required', false);
        }
    }

    function toggleConsultantFields() {
        if ($('input[name="uses_tax_consultant"]:checked').val() == '1') {
            $('#consultantFields').show();
            $('#consultantFields input, #consultantFields select').prop('required', true);
        } else {
            $('#consultantFields').hide();
            $('#consultantFields input, #consultantFields select').prop('required', false);
        }
    }

    // Event handlers
    $('#ptkpStatus').change(function() {
        const selectedOption = $(this).find('option:selected');
        const ptkpValue = selectedOption.data('ptkp-value');
    });

    $('input[name="is_audited"]').change(toggleAuditFields);
    $('input[name="uses_tax_consultant"]').change(toggleConsultantFields);
    
    $('input[name="npwp"], input[name="audit_firm_npwp"], input[name="auditor_npwp"], input[name="consultant_npwp"], input[name="consultant_firm_npwp"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/.{1,15}/g)[0];
            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
            $(this).val(value);
        }
    });
    
    // Submit form edit
    $('#editForm').submit(function(e) {
        e.preventDefault();
        
        var isValid = true;
        $('[required]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Form Tidak Lengkap',
                text: 'Harap lengkapi semua field yang wajib diisi'
            });
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
            return;
        }
        
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'SPT Tahunan berhasil diperbarui'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    });

    $(document).on('click', '.show-pdf-btn', function() {
        const reportId = $(this).data('id');
        const showUrl = $(this).data('show-url');
        
        $('#pdfPreviewModal').modal('show');
        $('#pdfPreviewFrame').attr('src', 'about:blank').hide();
        $('#pdfPreviewModal .modal-body').append('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Memuat SPT Tahunan...</p></div>');
        
        $.get(showUrl, function(response) {
            $('#pdfPreviewModal .modal-body').empty().html(`
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe id="pdfPreviewFrame" class="embed-responsive-item" src="${response.pdf_url}"></iframe>
                </div>
            `);
            $('#pdfDownloadBtn').attr('href', response.download_url);
        }).fail(function() {
            $('#pdfPreviewModal .modal-body').empty().html(`
                <div class="alert alert-danger">
                    Gagal memuat dokumen SPT
                </div>
            `);
        });
    });
});
</script>
@endpush
