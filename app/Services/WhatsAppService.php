<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send text message via Fonnte
     * 
     * @param string $phone Phone number (will be formatted automatically)
     * @param string $message Message text
     * @return array Response with success status
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            // Format phone number
            $phone = $this->formatPhone($phone);
            
            // Check if Fonnte token is configured
            if (empty(config('services.fonnte.token')) || config('services.fonnte.token') === 'your-fonnte-token-here') {
                Log::warning('Fonnte token not configured, using fallback', [
                    'phone' => $phone
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Fonnte token not configured',
                    'fallback_url' => $this->generateFallbackUrl($phone, $message)
                ];
            }
            
            // Send via Fonnte
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token')
            ])->post(config('services.fonnte.url'), [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Fonnte returns status: true on success
                if (($data['status'] ?? false) === true) {
                    Log::info('Fonnte message sent successfully', [
                        'phone' => $phone,
                        'message_id' => $data['id'] ?? null,
                        'detail' => $data['detail'] ?? null
                    ]);
                    
                    return [
                        'success' => true,
                        'messageId' => $data['id'] ?? null,
                        'message' => 'Message sent via Fonnte',
                        'detail' => $data['detail'] ?? null
                    ];
                }
                
                // Fonnte returned error
                Log::error('Fonnte returned error', [
                    'phone' => $phone,
                    'response' => $data
                ]);
                
                return [
                    'success' => false,
                    'error' => $data['reason'] ?? 'Unknown Fonnte error',
                    'fallback_url' => $this->generateFallbackUrl($phone, $message)
                ];
            }

            // HTTP request failed
            Log::error('Fonnte HTTP request failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Fonnte HTTP error: ' . $response->status(),
                'fallback_url' => $this->generateFallbackUrl($phone, $message)
            ];

        } catch (\Exception $e) {
            Log::error('Fonnte exception: ' . $e->getMessage(), [
                'phone' => $phone,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback_url' => $this->generateFallbackUrl($phone, $message)
            ];
        }
    }

    /**
     * Send media (image, PDF, etc) via Fonnte
     * 
     * @param string $phone Phone number
     * @param string $mediaUrl Full URL to media file
     * @param string|null $caption Optional caption
     * @param string|null $filename Optional filename
     * @return array Response with success status
     */
    public function sendMedia(string $phone, string $mediaUrl, ?string $caption = null, ?string $filename = null): array
    {
        try {
            $phone = $this->formatPhone($phone);
            
            if (empty(config('services.fonnte.token')) || config('services.fonnte.token') === 'your-fonnte-token-here') {
                return [
                    'success' => false,
                    'error' => 'Fonnte token not configured'
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token')
            ])->post(config('services.fonnte.url'), [
                'target' => $phone,
                'message' => $caption ?? '',
                'url' => $mediaUrl,
                'filename' => $filename,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (($data['status'] ?? false) === true) {
                    Log::info('Fonnte media sent successfully', [
                        'phone' => $phone,
                        'media_url' => $mediaUrl,
                        'message_id' => $data['id'] ?? null
                    ]);
                    
                    return [
                        'success' => true,
                        'messageId' => $data['id'] ?? null
                    ];
                }
            }

            Log::error('Failed to send Fonnte media', [
                'phone' => $phone,
                'media_url' => $mediaUrl,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send media via Fonnte'
            ];

        } catch (\Exception $e) {
            Log::error('Fonnte media exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    public function formatPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code 62 if needed
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Generate fallback wa.me URL if Fonnte fails
     */
    private function generateFallbackUrl(string $phone, string $message): string
    {
        $phone = $this->formatPhone($phone);
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
