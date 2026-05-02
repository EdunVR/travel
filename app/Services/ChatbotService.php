<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class ChatbotService
{
    /**
     * Process a message from a user and return chatbot response
     *
     * @param string $message The user's message
     * @param int $userId The ID of the user sending the message
     * @return string The chatbot's response
     */
    public function processMessage(string $message, int $userId): string
    {
        try {
            $response = $this->callAI($message);
            return $response;
        } catch (RequestException $e) {
            // Log API errors with details
            Log::error('Chatbot API error', [
                'user_id' => $userId,
                'message' => $message,
                'error' => $e->getMessage(),
                'status_code' => $e->response ? $e->response->status() : null,
                'timestamp' => now()->toDateTimeString(),
            ]);

            return $this->getFallbackResponse();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Log connection errors
            Log::error('Chatbot connection error', [
                'user_id' => $userId,
                'message' => $message,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return 'Maaf, saya tidak dapat terhubung ke server chatbot saat ini. Silakan beralih ke mode Superadmin untuk bantuan langsung.';
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Chatbot processing error', [
                'user_id' => $userId,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return 'Maaf, saya sedang mengalami gangguan teknis. Untuk bantuan segera, silakan beralih ke mode Superadmin dengan mengklik tombol di bagian atas.';
        }
    }

    /**
     * Get a response from the chatbot for a given message
     *
     * @param string $message The user's message
     * @return string The chatbot's response
     */
    public function getResponse(string $message): string
    {
        return $this->callAI($message);
    }

    /**
     * Call the AI API to get a response
     *
     * @param string $message The user's message
     * @return string The AI's response
     * @throws RequestException
     */
    private function callAI(string $message): string
    {
        $apiEndpoint = config('chatbot.api_endpoint');
        $apiKey = config('chatbot.api_key');
        $timeout = config('chatbot.timeout', 5);

        // If no API endpoint is configured, use rule-based responses
        if (empty($apiEndpoint)) {
            return $this->getRuleBasedResponse($message);
        }

        // Call external AI API with timeout (5 seconds max as per requirements)
        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($apiEndpoint, [
                'message' => $message,
                'context' => 'ERP MORRA Support',
            ]);

        if ($response->successful()) {
            return $response->json('response') ?? $this->getFallbackResponse();
        }

        throw new RequestException($response);
    }

    /**
     * Get a fallback response when AI fails
     *
     * @return string A fallback message
     */
    private function getFallbackResponse(): string
    {
        $fallbackResponses = [
            'Maaf, saya tidak dapat memproses permintaan Anda saat ini. Silakan coba lagi atau beralih ke mode Superadmin untuk bantuan langsung dari tim support kami.',
            'Mohon maaf, sistem chatbot sedang mengalami gangguan. Untuk bantuan segera, silakan beralih ke mode Superadmin dengan mengklik tombol di bagian atas.',
            'Saya mengalami kesulitan memahami pertanyaan Anda. Untuk bantuan yang lebih spesifik, silakan beralih ke mode Superadmin untuk berbicara langsung dengan tim kami.',
            'Sistem chatbot sedang tidak tersedia. Silakan beralih ke mode Superadmin untuk mendapatkan bantuan dari tim support kami.',
        ];

        return $fallbackResponses[array_rand($fallbackResponses)];
    }

    /**
     * Get a rule-based response for common questions
     *
     * @param string $message The user's message
     * @return string A rule-based response
     */
    private function getRuleBasedResponse(string $message): string
    {
        $message = strtolower(trim($message));

        // Common greetings
        if (preg_match('/^(hai|halo|hello|hi|selamat)/i', $message)) {
            return 'Halo! Selamat datang di ERP MORRA. Ada yang bisa saya bantu?';
        }

        // Help requests
        if (preg_match('/(bantuan|help|tolong)/i', $message)) {
            return 'Saya di sini untuk membantu Anda! Anda dapat menanyakan tentang:
- Cara menggunakan fitur ERP
- Informasi tentang modul yang tersedia
- Masalah teknis yang Anda alami

Atau Anda dapat beralih ke mode Superadmin untuk berbicara langsung dengan tim support kami.';
        }

        // Module questions
        if (preg_match('/(modul|fitur|menu)/i', $message)) {
            return 'ERP MORRA memiliki berbagai modul seperti:
- Finance (Keuangan)
- Inventory (Inventaris)
- Sales (Penjualan)
- Production (Produksi)
- HR/SDM (Sumber Daya Manusia)
- CRM (Customer Relationship Management)

Modul mana yang ingin Anda ketahui lebih lanjut?';
        }

        // Invoice questions
        if (preg_match('/(invoice|faktur|tagihan)/i', $message)) {
            return 'Untuk membuat invoice, Anda dapat:
1. Buka menu Penjualan
2. Pilih Invoice
3. Klik tombol "Tambah Invoice"
4. Isi data pelanggan dan item
5. Simpan dan cetak

Apakah ada yang ingin Anda tanyakan lebih lanjut tentang invoice?';
        }

        // Stock questions
        if (preg_match('/(stok|stock|persediaan|inventory)/i', $message)) {
            return 'Untuk mengelola stok:
1. Buka menu Inventaris
2. Pilih Produk atau Bahan
3. Anda dapat melihat, menambah, atau mengubah stok

Untuk transfer antar gudang, gunakan menu Transfer Gudang.';
        }

        // Report questions
        if (preg_match('/(laporan|report)/i', $message)) {
            return 'Laporan yang tersedia di ERP MORRA:
- Laporan Keuangan (Laba Rugi, Neraca, Cashflow)
- Laporan Penjualan
- Laporan Stok
- Laporan Produksi
- Laporan HR/Payroll

Laporan mana yang Anda butuhkan?';
        }

        // Default response
        return 'Terima kasih atas pertanyaan Anda. Untuk informasi lebih detail atau bantuan khusus, silakan hubungi superadmin dengan beralih ke mode Superadmin.';
    }
}
