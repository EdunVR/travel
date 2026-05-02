<?php

namespace App\Http\Controllers;

use App\Models\AnnualTaxReport;
use App\Models\AccountingBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AnnualTaxReportController extends Controller
{
    public function index()
    {
        $reports = AnnualTaxReport::with(['accountingBook', 'creator'])
            ->orderBy('report_year', 'desc')
            ->paginate(20);
        $books = AccountingBook::active()->get();

        return view('financial.annual-tax-report.index', compact('reports', 'books'));
    }

    public function create()
    {
        $books = AccountingBook::active()->get();
        return view('financial.annual-tax-report.create', compact('books'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except('taxpayer_signature', 'consultant_signature', 'tax_rates', '_token', 'ptkp_value');
            
            // Handle file upload
            if ($request->hasFile('taxpayer_signature')) {
                // Simpan file di folder public/img/signatures
                $filename = 'signature_'.time().'.'.$request->file('taxpayer_signature')->extension();
                $request->file('taxpayer_signature')->move(public_path('img/signatures'), $filename);
                $data['taxpayer_signature'] = 'img/signatures/'.$filename;
            }

            // Optimalkan JSON encoding
            $data['tax_object'] = json_encode($request->tax_object ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $data['tax_rate_data'] = json_encode($request->tax_rates ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            // Pastikan NPWP 15 digit
            $data['npwp'] = substr(preg_replace('/[^0-9]/', '', $data['npwp'] ?? ''), 0, 15);
            
            $data['created_by'] = auth()->id();
            
            // Debug final data
            Log::debug('Final data before save:', $data);
            
            $annualTaxReport = AnnualTaxReport::create($data);
            
            DB::commit();
            
            return redirect()->route('financial.annual-tax-report.index')
                ->with('success', 'SPT Tahunan berhasil disimpan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving annual tax report: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan SPT Tahunan: '.$e->getMessage())
                ->withInput();
        }
    }

    protected function validateReport(Request $request)
    {
        return Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'report_year' => 'required|digits:4|integer|min:2000|max:'.(date('Y')+1),
            'tax_object' => 'required|in:final,specific_gross_turnover,general_article17',
            'ptkp_status' => 'required|in:K/0,K/1,K/2,K/3,K/I/0,K/I/1,K/I/2,K/I/3,TK/0,TK/1,TK/2,TK/3',
            'tax_rate_data' => 'required|array',
            'marital_tax_status' => 'required|in:KK,HB,PH,MT',
            'npwp' => 'required|regex:/^\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}$/',
            'taxpayer_name' => 'required|string|max:255',
            'business_type' => 'required|in:service,perpetual_trade,periodic_trade,service_perpetual_trade,service_periodic_trade',
            'klu_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'accounting_period' => 'required|string',
            'taxpayer_signature' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'is_audited' => 'boolean',
            'audit_opinion' => 'required_if:is_audited,true|nullable|in:unqualified,qualified,adverse,no_opinion',
            'uses_tax_consultant' => 'boolean',
            'consultant_name' => 'required_if:uses_tax_consultant,true|nullable|string|max:255',
            'consultant_npwp' => 'required_if:uses_tax_consultant,true|nullable|regex:/^\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}$/',
            'consultant_signature' => 'required_if:uses_tax_consultant,true|nullable|file|mimes:jpg,png,pdf|max:2048',
        ], [
            'npwp.regex' => 'Format NPWP tidak valid. Contoh: 12.345.678.9-012.345',
            'consultant_npwp.regex' => 'Format NPWP konsultan tidak valid. Contoh: 12.345.678.9-012.345',
            'tax_rate_data.required' => 'Data tarif pajak harus diisi',
            'audit_opinion.required_if' => 'Opini audit harus dipilih jika laporan diaudit',
        ]);
    }

    public function edit($id)
    {
        $report = AnnualTaxReport::with('accountingBook')->findOrFail($id);

        Log::info('Data SPT untuk edit:', [
            'tax_object' => $report->tax_object,
            'tax_rate_data' => $report->tax_rate_data,
            'decoded_tax_object' => json_decode($report->tax_object, true),
            'decoded_tax_rate' => json_decode($report->tax_rate_data, true)
        ]);
        
        // Konversi data ke format yang siap digunakan di form
        return response()->json([
            'accounting_book_id' => $report->accounting_book_id,
            'report_year' => $report->report_year,
            'tax_object' => json_decode($report->tax_object, true) ?? [],
            'ptkp_status' => $report->ptkp_status,
            'marital_tax_status' => $report->marital_tax_status,
            'npwp' => $report->npwp,
            'taxpayer_name' => $report->taxpayer_name,
            'business_field' => $report->business_field,
            'business_type' => $report->business_type,
            'klu_code' => $report->klu_code,
            'phone' => $report->phone,
            'accounting_period' => $report->accounting_period,
            'revision_number' => $report->revision_number,
            'head_office_country' => $report->head_office_country,
            'is_audited' => $report->is_audited,
            'audit_opinion' => $report->audit_opinion,
            'audit_firm_name' => $report->audit_firm_name,
            'audit_firm_npwp' => $report->audit_firm_npwp,
            'auditor_name' => $report->auditor_name,
            'auditor_npwp' => $report->auditor_npwp,
            'uses_tax_consultant' => $report->uses_tax_consultant,
            'consultant_name' => $report->consultant_name,
            'consultant_npwp' => $report->consultant_npwp,
            'consultant_firm_name' => $report->consultant_firm_name,
            'consultant_firm_npwp' => $report->consultant_firm_npwp,
            'has_fiscal_loss_compensation' => $report->has_fiscal_loss_compensation,
            'has_related_party_transactions' => $report->has_related_party_transactions,
            'has_investment_facilities' => $report->has_investment_facilities,
            'has_main_branches' => $report->has_main_branches,
            'has_foreign_income' => $report->has_foreign_income,
            'financial_statement_type' => $report->financial_statement_type,
            'tax_rate_data' => json_decode($report->tax_rate_data, true) ?? [],
            'taxpayer_signature' => $report->taxpayer_signature,
            'audit_firm_signature' => $report->audit_firm_signature,
            'auditor_signature' => $report->auditor_signature,
            'consultant_signature' => $report->consultant_signature,
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = AnnualTaxReport::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $data = $request->except('_token', '_method', 'taxpayer_signature', 'consultant_signature', 'tax_rates', 'remove_signature');
            
            // 1. Handle tanda tangan wajib pajak
            if ($request->hasFile('taxpayer_signature')) {
                // Hapus file lama jika ada
                if ($report->taxpayer_signature && file_exists(public_path($report->taxpayer_signature))) {
                    unlink(public_path($report->taxpayer_signature));
                }
                
                // Simpan file baru
                $filename = 'signature_'.time().'.'.$request->file('taxpayer_signature')->extension();
                $request->file('taxpayer_signature')->move(public_path('img/signatures'), $filename);
                $data['taxpayer_signature'] = 'img/signatures/'.$filename;
            } elseif ($request->remove_signature == '1') {
                // Hapus file jika diminta
                if ($report->taxpayer_signature && file_exists(public_path($report->taxpayer_signature))) {
                    unlink(public_path($report->taxpayer_signature));
                }
                $data['taxpayer_signature'] = null;
            }

            // 2. Handle tanda tangan konsultan pajak
            if ($request->hasFile('consultant_signature')) {
                // Hapus file lama jika ada
                if ($report->consultant_signature && file_exists(public_path($report->consultant_signature))) {
                    unlink(public_path($report->consultant_signature));
                }
                
                // Simpan file baru
                $filename = 'signature_'.time().'.'.$request->file('consultant_signature')->extension();
                $request->file('consultant_signature')->move(public_path('img/signatures'), $filename);
                $data['consultant_signature'] = 'img/signatures/'.$filename;
            }
            
            // Konversi data array ke JSON
            $data['tax_object'] = json_encode($request->tax_object ?? []);
            $data['tax_rate_data'] = json_encode($request->tax_rates ?? []);
            
            $report->update($data);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'SPT Tahunan berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui SPT Tahunan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $report = AnnualTaxReport::with(['accountingBook', 'creator'])->findOrFail($id);
        
        // Decode JSON fields
        $taxRates = json_decode($report->tax_rate_data, true) ?? [];
        $taxObjects = json_decode($report->tax_object, true) ?? [];

        $data = [
            'report' => $report,
            'tax_rates' => $taxRates,
            'tax_objects' => $taxObjects, // Pastikan ini dikirim
            'ptkp_label' => \App\Models\AnnualTaxReport::PTKP_STATUSES[$report->ptkp_status] ?? $report->ptkp_status,
            'current_date' => now()->format('d F Y'),
            'business_type_label' => $report->getBusinessTypeLabel(),
            'marital_status_label' => $report->getMaritalStatusLabel(),
            'gross_income' => $report->gross_income ?? 0,
            'net_income' => $report->net_income ?? 0,
            'tax_withheld' => $report->tax_withheld ?? 0,
            'ptkp_value' => $report->ptkp_value ?? 0
        ];
        
        $pdf = Pdf::loadView('financial.annual-tax-report.pdf_template', $data);
        
        return response()->json([
            'pdf_url' => 'data:application/pdf;base64,'.base64_encode($pdf->output()),
            'download_url' => route('financial.annual-tax-report.download', $id)
        ]);
    }

    public function download($id)
    {
        $report = AnnualTaxReport::findOrFail($id);
        $pdf = Pdf::loadView('financial.annual-tax-report.pdf_template', [
            'report' => $report,
            'ptkp_label' => AnnualTaxReport::PTKP_STATUSES[$report->ptkp_status] ?? $report->ptkp_status,
            'current_date' => now()->format('d F Y'),
            'tax_rate_data' => json_decode($report->tax_rate_data, true),
            'tax_object' => json_decode($report->tax_object, true),
            'taxpayer_signature' => $report->taxpayer_signature,
            'audit_firm_signature' => $report->audit_firm_signature,
            'auditor_signature' => $report->auditor_signature,
            'consultant_signature' => $report->consultant_signature
        ]);
        
        return $pdf->download('SPT-Tahunan-'.$report->report_year.'-'.$report->taxpayer_name.'.pdf');
    }

    // Tambahkan di dalam class AnnualTaxReportController
    protected function getBusinessTypeLabel($type)
    {
        $labels = [
            'service' => 'Jasa',
            'perpetual_trade' => 'Dagang Perpetual', 
            'periodic_trade' => 'Dagang Periodik',
            'service_perpetual_trade' => 'Jasa & Dagang Perpetual',
            'service_periodic_trade' => 'Jasa & Dagang Periodik'
        ];
        return $labels[$type] ?? $type;
    }

    protected function getMaritalStatusLabel($status)
    {
        $labels = [
            'KK' => 'KK - Kewajiban Bersama',
            'HB' => 'HB - Hidup Berpisah',
            'PH' => 'PH - Pemisahan Harta',
            'MT' => 'MT - Kewajiban Terpisah'
        ];
        return $labels[$status] ?? $status;
    }
}