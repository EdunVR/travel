{{-- resources/views/partials/print-footer.blade.php --}}
<div class="print-footer" style="margin-top: 30px; page-break-inside: avoid;">
    
    {{-- Bank Information --}}
    @if($companySettings['bank_name'] || $companySettings['bank_account_number'])
    <div class="bank-info" style="margin-bottom: 20px; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background-color: #f8f9fa;">
        <div class="bank-title" style="font-weight: bold; margin-bottom: 8px; color: #2c3e50; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
            INFORMASI PEMBAYARAN
        </div>
        
        @if($companySettings['bank_name'])
        <div style="margin-bottom: 3px;">
            <strong>Bank:</strong> {{ $companySettings['bank_name'] }}
        </div>
        @endif
        
        @if($companySettings['bank_account_number'])
        <div style="margin-bottom: 3px;">
            <strong>No. Rekening:</strong> <span style="font-family: monospace;">{{ $companySettings['bank_account_number'] }}</span>
        </div>
        @endif
        
        @if($companySettings['bank_account_name'])
        <div style="margin-bottom: 3px;">
            <strong>Atas Nama:</strong> {{ $companySettings['bank_account_name'] }}
        </div>
        @endif
    </div>
    @endif
    
    {{-- Legal Information --}}
    @if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup'] || $companySettings['tdp'])
    <div class="legal-info" style="margin-bottom: 20px; font-size: 10px; color: #6c757d; text-align: center;">
        @if($companySettings['npwp'])
            NPWP: {{ $companySettings['npwp'] }}
        @endif
        
        @if($companySettings['nib'])
            @if($companySettings['npwp']) | @endif
            NIB: {{ $companySettings['nib'] }}
        @endif
        
        @if($companySettings['siup'])
            @if($companySettings['npwp'] || $companySettings['nib']) | @endif
            SIUP: {{ $companySettings['siup'] }}
        @endif
        
        @if($companySettings['tdp'])
            @if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup']) | @endif
            TDP: {{ $companySettings['tdp'] }}
        @endif
    </div>
    @endif
    
    {{-- Signature Section --}}
    <div class="signature-section" style="display: table; width: 100%; margin-top: 40px;">
        <div class="signature-left" style="display: table-cell; width: 50%; vertical-align: top;">
            @if(isset($showCustomerSignature) && $showCustomerSignature)
            <div style="text-align: center;">
                <div style="margin-bottom: 60px;">Penerima,</div>
                <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto; padding-top: 5px;">
                    ( ........................... )
                </div>
            </div>
            @endif
        </div>
        
        <div class="signature-right" style="display: table-cell; width: 50%; vertical-align: top; text-align: center;">
            <div style="margin-bottom: 10px;">
                {{ $companySettings['company_address'] ? explode(',', $companySettings['company_address'])[0] : 'Jakarta' }}, 
                {{ isset($documentDate) ? $documentDate : date('d F Y') }}
            </div>
            <div style="margin-bottom: 60px;">Hormat Kami,</div>
            <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto; padding-top: 5px;">
                <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
            </div>
        </div>
    </div>
    
    {{-- Footer Note --}}
    <div class="footer-note" style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d; border-top: 1px dashed #dee2e6; padding-top: 10px;">
        Dokumen ini dicetak secara otomatis oleh sistem {{ $companySettings['company_name'] }}
        <br>
        Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

<style>
    @media print {
        .print-footer {
            page-break-inside: avoid;
        }
    }
</style>