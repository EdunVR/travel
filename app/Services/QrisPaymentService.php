<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QrisPaymentService
{
    protected string $apiKey;
    protected string $mID;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.qris.api_key');
        $this->mID = config('services.qris.merchant_id');
        $this->baseUrl = config('services.qris.base_url', 'https://qris.interactive.co.id/restapi/qris/show_qris.php');
    }

    /**
     * Generate QRIS Invoice
     * 
     * API: https://qris.interactive.co.id/api-doc/create-invoice.php
     * 
     * @param string $trxNumber Unique transaction number from client
     * @param int $amount Transaction amount in IDR
     * @return array Response from API
     */
    public function createInvoice(string $trxNumber, int $amount): array
    {
        $maxRetries = 2;
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $postData = [
                    'do'            => 'create-invoice',
                    'apikey'        => $this->apiKey,
                    'mID'           => $this->mID,
                    'cliTrxNumber'  => $trxNumber,
                    'cliTrxAmount'  => $amount,
                    'useTip'        => 'no',
                ];

                Log::info('QRIS Create Invoice Request', [
                    'attempt' => $attempt,
                    'url' => $this->baseUrl,
                    'params' => $postData,
                ]);

                // API uses GET method with query parameters
                $response = Http::timeout(30)
                    ->connectTimeout(15)
                    ->withOptions([
                        'curl' => [
                            CURLOPT_RESOLVE => ['qris.interactive.co.id:443:13.75.115.40'],
                        ],
                    ])
                    ->get($this->baseUrl, $postData);

                $result = $response->json();

                Log::info('QRIS Create Invoice Response', [
                    'trx_number' => $trxNumber,
                    'amount' => $amount,
                    'attempt' => $attempt,
                    'response' => $result,
                ]);

                // Check for API-level failure (status: failed)
                if (isset($result['status']) && $result['status'] === 'failed') {
                    $errorMsg = is_string($result['data'] ?? null) ? $result['data'] : ($result['message'] ?? 'Unknown API error');
                    $lastError = $errorMsg;
                    
                    Log::warning('QRIS API returned failure', [
                        'trx_number' => $trxNumber,
                        'amount' => $amount,
                        'attempt' => $attempt,
                        'error' => $errorMsg,
                        'content_type' => $attempt === 1 ? 'json' : 'form',
                    ]);
                    continue;
                }

                if ($response->successful() && isset($result['data']) && is_array($result['data'])) {
                    return [
                        'success' => true,
                        'data' => $result['data'],
                        'qris_content' => $result['data']['qris_content'] ?? null,
                        'qris_request_date' => $result['data']['qris_request_date'] ?? null,
                        'qris_invoiceid' => $result['data']['qris_invoiceid'] ?? null,
                        'nmid' => $result['data']['nmid'] ?? null,
                    ];
                }

                $lastError = $result['message'] ?? ($result['data'] ?? 'Gagal membuat QRIS invoice');

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning('QRIS Create Invoice attempt failed', [
                    'trx_number' => $trxNumber,
                    'amount' => $amount,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                // Wait before retry
                if ($attempt < $maxRetries) {
                    sleep(2);
                }
            }
        }

        Log::error('QRIS Create Invoice Error (all retries failed)', [
            'trx_number' => $trxNumber,
            'amount' => $amount,
            'error' => $lastError,
        ]);

        return [
            'success' => false,
            'message' => 'Koneksi ke server QRIS gagal: ' . $lastError,
        ];
    }

    /**
     * Check QRIS Invoice Payment Status
     * 
     * API: https://qris.interactive.co.id/api-doc/check-invoice.php
     * 
     * @param string $invoiceId QRIS Invoice ID from createInvoice response
     * @param string $trxNumber Client transaction number
     * @param string $trxDate Transaction date (format: Y-m-d)
     * @return array Response from API
     */
    public function checkInvoice(string $invoiceId, string $trxNumber, string $trxDate): array
    {
        try {
            $checkUrl = config('services.qris.check_url', 'https://qris.interactive.co.id/restapi/qris/checkpaid_qris.php');

            $response = Http::timeout(30)
                ->connectTimeout(15)
                ->withOptions([
                    'curl' => [
                        CURLOPT_RESOLVE => ['qris.interactive.co.id:443:13.75.115.40'],
                    ],
                ])
                ->get($checkUrl, [
                    'do'              => 'checkStatus',
                    'apikey'          => $this->apiKey,
                    'mID'             => $this->mID,
                    'invid'           => $invoiceId,
                    'trxvalue'        => $trxNumber,
                    'trxdate'         => $trxDate,
                ]);

            $result = $response->json();

            Log::info('QRIS Check Invoice Response', [
                'invoice_id' => $invoiceId,
                'trx_number' => $trxNumber,
                'trx_date' => $trxDate,
                'response' => $result,
            ]);

            if ($response->successful() && isset($result['data'])) {
                $data = $result['data'];
                $isPaid = ($data['qris_status'] ?? '') === 'paid';

                return [
                    'success' => true,
                    'paid' => $isPaid,
                    'status' => $data['qris_status'] ?? 'unpaid',
                    'data' => $data,
                    'qris_payment_customername' => $data['qris_payment_customername'] ?? null,
                    'qris_payment_methodby' => $data['qris_payment_methodby'] ?? null,
                ];
            }

            return [
                'success' => false,
                'paid' => false,
                'message' => $result['message'] ?? 'Gagal cek status QRIS',
                'raw' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('QRIS Check Invoice Error', [
                'invoice_id' => $invoiceId,
                'trx_number' => $trxNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'paid' => false,
                'message' => 'Koneksi ke server QRIS gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a unique transaction number for QRIS
     * 
     * @param int $bookingId
     * @param string $prefix
     * @return string
     */
    public function generateTrxNumber(int $bookingId, string $prefix = 'HMT'): string
    {
        return $prefix . '-' . $bookingId . '-' . now()->format('YmdHis') . '-' . rand(100, 999);
    }
}
