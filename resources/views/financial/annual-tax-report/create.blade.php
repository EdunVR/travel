<style>
    /* resources/css/app.css atau bagian style di head */
    .btn-add-spt {
        transition: all 0.3s ease;
    }
    .btn-add-spt:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .select-ptkp {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        padding-right: 2.5rem;
    }
</style>

@extends('app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Buat SPT Tahunan Baru
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('financial.annual-tax-report.store') }}" method="POST" enctype="multipart/form-data" id="sptForm">
                @csrf
                
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
                            <small class="text-muted">Format: JPG, PNG, PDF (max 2MB)</small>
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
                
                <div class="text-center">
                    <a href="{{ route('financial.annual-tax-report.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan SPT Tahunan
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update nilai PTKP saat status PTKP berubah
    $('#ptkpStatus').change(function() {
        const selectedOption = $(this).find('option:selected');
        const ptkpValue = selectedOption.data('ptkp-value');
    }).trigger('change');

    // Format mata uang Rupiah
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

    // Fungsi untuk menangani tampilan field audit
    function toggleAuditFields() {
        if ($('input[name="is_audited"]:checked').val() == '1') {
            $('#auditFields').show();
            $('#auditFields select, #auditFields input').prop('required', true);
        } else {
            $('#auditFields').hide();
            $('#auditFields select, #auditFields input').prop('required', false);
        }
    }

    // Fungsi untuk menangani tampilan field konsultan
    function toggleConsultantFields() {
        if ($('input[name="uses_tax_consultant"]:checked').val() == '1') {
            $('#consultantFields').show();
            $('#consultantFields input, #consultantFields select').prop('required', true);
        } else {
            $('#consultantFields').hide();
            $('#consultantFields input, #consultantFields select').prop('required', false);
        }
    }

    // Inisialisasi pertama kali
    toggleAuditFields();
    toggleConsultantFields();

    // Event handler untuk radio button audit
    $('input[name="is_audited"]').change(toggleAuditFields);
    
    // Event handler untuk radio button konsultan
    $('input[name="uses_tax_consultant"]').change(toggleConsultantFields);
    
    // Format input NPWP
    $('input[name="npwp"], input[name="audit_firm_npwp"], input[name="auditor_npwp"], input[name="consultant_npwp"], input[name="consultant_firm_npwp"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/.{1,15}/g)[0];
            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
            $(this).val(value);
        }
    });
    
    // Validasi form sebelum submit
    $('#sptForm').submit(function(e) {
        var isValid = true;
        
        // Validasi required fields
        $('[required]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Form Tidak Lengkap',
                text: 'Harap lengkapi semua field yang wajib diisi',
                scrollbarPadding: false
            });
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
});
</script>
@endpush
