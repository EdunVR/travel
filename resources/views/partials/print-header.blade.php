{{-- resources/views/partials/print-header.blade.php --}}
<div class="print-header" style="display: table; width: 100%; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px;">
    <div class="header-left" style="display: table-cell; width: 70%; vertical-align: top;">
        @if($companySettings['logo_url'])
        <img src="{{ $companySettings['logo_url'] }}" 
             alt="Company Logo" 
             style="width: 80px; height: auto; float: left; margin-right: 15px; margin-top: 5px;">
        @endif
        
        <div class="company-info" style="overflow: hidden;">
            <div class="company-name" style="font-weight: bold; font-size: 18px; margin-bottom: 5px; color: #2c3e50;">
                {{ $companySettings['company_name'] }}
            </div>
            
            @if($companySettings['company_code'])
            <div class="company-code" style="font-size: 12px; color: #7f8c8d; margin-bottom: 3px;">
                Kode: {{ $companySettings['company_code'] }}
            </div>
            @endif
            
            @if($companySettings['company_address'])
            <div class="company-address" style="font-size: 12px; line-height: 1.4; color: #34495e; margin-bottom: 3px;">
                {!! $companySettings['formatted_address'] !!}
            </div>
            @endif
            
            <div class="company-contact" style="font-size: 12px; color: #34495e;">
                @if($companySettings['company_phone'])
                    <span>Telp: {{ $companySettings['company_phone'] }}</span>
                @endif
                
                @if($companySettings['company_email'])
                    @if($companySettings['company_phone']) | @endif
                    <span>Email: {{ $companySettings['company_email'] }}</span>
                @endif
                
                @if($companySettings['company_website'])
                    @if($companySettings['company_phone'] || $companySettings['company_email']) | @endif
                    <span>{{ $companySettings['company_website'] }}</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="header-right" style="display: table-cell; width: 30%; vertical-align: top; text-align: right;">
        <div class="document-info">
            @if(isset($documentTitle))
            <div class="document-title" style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: #e74c3c;">
                {{ $documentTitle }}
            </div>
            @endif
            
            @if(isset($documentNumber))
            <div class="document-number" style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">
                No: {{ $documentNumber }}
            </div>
            @endif
            
            @if(isset($documentDate))
            <div class="document-date" style="font-size: 12px; color: #7f8c8d;">
                Tanggal: {{ $documentDate }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        .print-header {
            page-break-inside: avoid;
        }
        
        .print-header img {
            max-width: 80px !important;
            height: auto !important;
        }
    }
</style>