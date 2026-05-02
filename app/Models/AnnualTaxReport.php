<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AnnualTaxReport extends Model
{
    protected $fillable = [
        'accounting_book_id',
        'report_year',
        'tax_object',
        'ptkp_status',
        'ptkp_value',
        'marital_tax_status',
        'npwp',
        'taxpayer_name',
        'business_field',
        'business_type',
        'klu_code',
        'phone',
        'accounting_period',
        'revision_number',
        'head_office_country',
        'taxpayer_signature',
        'is_audited',
        'audit_opinion',
        'audit_firm_name',
        'audit_firm_npwp',
        'auditor_name',
        'auditor_npwp',
        'uses_tax_consultant',
        'consultant_name',
        'consultant_npwp',
        'consultant_firm_name',
        'consultant_firm_npwp',
        'consultant_signature',
        'has_fiscal_loss_compensation',
        'has_related_party_transactions',
        'has_investment_facilities',
        'has_main_branches',
        'has_foreign_income',
        'financial_statement_type',
        'tax_rate_data',
        'created_by',
        'is_completed'
    ];

    protected $casts = [
        'tax_object' => 'array',
        'tax_rate_data' => 'array',
        'is_audited' => 'boolean',
        'uses_tax_consultant' => 'boolean',
        'has_fiscal_loss_compensation' => 'boolean',
        'has_related_party_transactions' => 'boolean',
        'has_investment_facilities' => 'boolean',
        'has_main_branches' => 'boolean',
        'has_foreign_income' => 'boolean',
    ];

    const PTKP_STATUSES = [
        'TK/0' => 'TK/0 - Tidak Kawin/Tanggungan 0 (Rp54.000.000)',
        'TK/1' => 'TK/1 - Tidak Kawin/Tanggungan 1 (Rp58.500.000)',
        'TK/2' => 'TK/2 - Tidak Kawin/Tanggungan 2 (Rp63.000.000)',
        'TK/3' => 'TK/3 - Tidak Kawin/Tanggungan 3 (Rp67.500.000)',
        'K/0' => 'K/0 - Kawin/Tanggungan 0 (Rp58.500.000)',
        'K/1' => 'K/1 - Kawin/Tanggungan 1 (Rp63.000.000)',
        'K/2' => 'K/2 - Kawin/Tanggungan 2 (Rp67.500.000)',
        'K/3' => 'K/3 - Kawin/Tanggungan 3 (Rp72.000.000)',
        'K/I/0' => 'K/I/0 - Kawin/Istri bekerja/Tanggungan 0 (Rp112.500.000)',
        'K/I/1' => 'K/I/1 - Kawin/Istri bekerja/Tanggungan 1 (Rp117.000.000)',
        'K/I/2' => 'K/I/2 - Kawin/Istri bekerja/Tanggungan 2 (Rp121.500.000)',
        'K/I/3' => 'K/I/3 - Kawin/Istri bekerja/Tanggungan 3 (Rp126.000.000)'
    ];

    protected $appends = ['business_type_label', 'marital_status_label'];


    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor untuk PTKP
    protected function ptkpLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'K/0' => 'Kawin/Tanggungan 0',
                    'K/1' => 'Kawin/Tanggungan 1',
                    'K/2' => 'Kawin/Tanggungan 2',
                    'K/3' => 'Kawin/Tanggungan 3',
                    'K/I/0' => 'Kawin/Istri bekerja/Tanggungan 0',
                    'K/I/1' => 'Kawin/Istri bekerja/Tanggungan 1',
                    'K/I/2' => 'Kawin/Istri bekerja/Tanggungan 2',
                    'K/I/3' => 'Kawin/Istri bekerja/Tanggungan 3',
                    'TK/0' => 'Tidak Kawin/Tanggungan 0',
                    'TK/1' => 'Tidak Kawin/Tanggungan 1',
                    'TK/2' => 'Tidak Kawin/Tanggungan 2',
                    'TK/3' => 'Tidak Kawin/Tanggungan 3',
                ];
                return $labels[$this->ptkp_status] ?? $this->ptkp_status;
            }
        );
    }

    protected function taxObject(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true) ?? [],
            set: fn ($value) => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    public function getBusinessTypeLabel()
    {
        $labels = [
            'service' => 'Jasa',
            'perpetual_trade' => 'Dagang Perpetual',
            'periodic_trade' => 'Dagang Periodik',
            'service_perpetual_trade' => 'Jasa & Dagang Perpetual',
            'service_periodic_trade' => 'Jasa & Dagang Periodik'
        ];
        return $labels[$this->business_type] ?? $this->business_type;
    }

    public function getMaritalStatusLabel() 
    {
        $labels = [
            'KK' => 'KK - Kewajiban Bersama',
            'HB' => 'HB - Hidup Berpisah',
            'PH' => 'PH - Pemisahan Harta',
            'MT' => 'MT - Kewajiban Terpisah'
        ];
        return $labels[$this->marital_tax_status] ?? $this->marital_tax_status;
    }

    public function getBusinessTypeLabelAttribute()
    {
        $labels = [
            'service' => 'Jasa',
            'perpetual_trade' => 'Dagang Perpetual',
            'periodic_trade' => 'Dagang Periodik',
            'service_perpetual_trade' => 'Jasa & Dagang Perpetual',
            'service_periodic_trade' => 'Jasa & Dagang Periodik'
        ];
        return $labels[$this->business_type] ?? $this->business_type;
    }

    public function getMaritalStatusLabelAttribute()
    {
        $labels = [
            'KK' => 'KK - Kewajiban Bersama',
            'HB' => 'HB - Hidup Berpisah',
            'PH' => 'PH - Pemisahan Harta',
            'MT' => 'MT - Kewajiban Terpisah'
        ];
        return $labels[$this->marital_tax_status] ?? $this->marital_tax_status;
    }
}