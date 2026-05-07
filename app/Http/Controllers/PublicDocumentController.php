<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\JamaahPayment;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicDocumentController extends Controller
{
    /**
     * Display full history page
     */
    public function sejarah()
    {
        return view('public.sejarah');
    }

    /**
     * Display all legality documents page
     */
    public function legalitas()
    {
        return view('public.legalitas');
    }

    /**
     * Display all hotel partners page
     */
    public function hotelPartner()
    {
        return view('public.hotel-partner');
    }

    /**
     * Public invoice PDF — accessible without login via signed token.
     * URL: /public/invoice/{booking}/{token}
     */
    public function invoice($bookingId, $token)
    {
        $booking = JamaahBooking::find($bookingId);

        if (!$booking || !$booking->id_invoice) {
            abort(404, 'Invoice tidak ditemukan');
        }

        // Verify token: sha256(booking_id + invoice_id + APP_KEY)
        $expected = hash('sha256', $booking->id . $booking->id_invoice . config('app.key'));
        if (!hash_equals($expected, $token)) {
            abort(403, 'Token tidak valid');
        }

        try {
            $invoice = SalesInvoice::with(['member', 'items', 'outlet'])
                ->findOrFail($booking->id_invoice);

            $booking->load([
                'travelPackage.flightDeparture',
                'travelPackage.flightReturn',
                'travelPackage.hotelMakkah',
                'travelPackage.hotelMadinah',
                'jamaah',
                'keberangkatan',
                'closedBy',
                'outlet',
                'payments',
                'addons',
                'hotelBookings.hotel'
            ]);

            // Use same method as receipt for consistency
            $companySettings = $booking->getCompanySettings();

            // Bank accounts
            $bankAccounts = collect();
            try {
                $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $booking->id_outlet)
                    ->where('is_active', 1)->orderBy('sort_order')->get();
                if ($bankAccounts->isEmpty()) {
                    $bankAccounts = \App\Models\CompanyBankAccount::whereNull('id_outlet')
                        ->where('is_active', 1)->orderBy('sort_order')->get();
                }
            } catch (\Exception $e) {}

            $termsConditions = $booking->terms_conditions ?? null;

            $pdf = PDF::loadView('admin.travel.payment.jamaah-invoice-pdf', [
                'invoice'          => $invoice,
                'booking'          => $booking,
                'companySettings'  => $companySettings,
                'bankAccounts'     => $bankAccounts,
                'termsConditions'  => $termsConditions,
            ])->setPaper('a4', 'portrait');

            return $pdf->stream('Invoice-' . $invoice->no_invoice . '.pdf');

        } catch (\Exception $e) {
            Log::error('Public invoice error: ' . $e->getMessage());
            abort(500, 'Gagal memuat invoice');
        }
    }

    /**
     * Public receipt PDF — accessible without login via signed token.
     * URL: /public/receipt/{payment}/{token}
     */
    public function receipt($paymentId, $token)
    {
        $payment = JamaahPayment::with(['jamaahBooking.jamaah', 'jamaahBooking.travelPackage', 'recordedBy'])
            ->find($paymentId);

        if (!$payment) {
            abort(404, 'Kwitansi tidak ditemukan');
        }

        // Verify token: sha256(payment_id + booking_id + APP_KEY)
        $expected = hash('sha256', $payment->id . $payment->id_jamaah_booking . config('app.key'));
        if (!hash_equals($expected, $token)) {
            abort(403, 'Token tidak valid');
        }

        try {
            $companySettings = $payment->jamaahBooking->getCompanySettings();

            $pdf = PDF::loadView('admin.travel.payment.receipt-pdf', compact('payment', 'companySettings'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Kwitansi-' . $payment->receipt_number . '.pdf');

        } catch (\Exception $e) {
            Log::error('Public receipt error: ' . $e->getMessage());
            abort(500, 'Gagal memuat kwitansi');
        }
    }

    /**
     * Helper: get company settings for a given outlet (static-ish, no auth needed)
     */
    protected function getCompanySettingsForPrint($outletId = null)
    {
        try {
            // Try outlet-specific settings first
            $settings = null;
            if ($outletId) {
                $settings = \App\Models\CompanySetting::where('outlet_id', $outletId)->first();
            }
            if (!$settings) {
                $settings = \App\Models\CompanySetting::first();
            }
            if (!$settings) return $this->getDefaultSettings();

            return [
                'company_name'        => $settings->company_name ?? config('app.name'),
                'company_address'     => $settings->company_address ?? '',
                'formatted_address'   => $settings->formatted_address ?? $settings->company_address ?? '',
                'company_phone'       => $settings->company_phone ?? '',
                'formatted_phone'     => $settings->formatted_phone ?? $settings->company_phone ?? '',
                'company_email'       => $settings->company_email ?? '',
                'company_logo'        => $settings->company_logo ?? null,
                'bank_name'           => $settings->bank_name ?? '',
                'bank_account_number' => $settings->bank_account_number ?? '',
                'bank_account_name'   => $settings->bank_account_name ?? '',
            ];
        } catch (\Exception $e) {
            return $this->getDefaultSettings();
        }
    }

    private function getDefaultSettings(): array
    {
        return [
            'company_name'       => config('app.name'),
            'company_address'    => '',
            'formatted_address'  => '',
            'company_phone'      => '',
            'formatted_phone'    => '',
            'company_email'      => '',
            'company_logo'       => null,
            'bank_name'          => '',
            'bank_account_number'=> '',
            'bank_account_name'  => '',
        ];
    }
}
