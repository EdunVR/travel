<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleVisionService
{
    private string $apiKey;
    private string $imageEndpoint = 'https://vision.googleapis.com/v1/images:annotate';
    private string $fileEndpoint  = 'https://vision.googleapis.com/v1/files:annotate';

    public function __construct()
    {
        $this->apiKey = config('services.google_vision.api_key', env('GOOGLE_VISION_API_KEY', ''));
    }

    /**
     * Extract text from image or PDF file using Google Cloud Vision API.
     *
     * - Images (JPG, PNG, etc.) → images:annotate endpoint
     * - PDF / TIFF              → files:annotate endpoint (supports multi-page)
     *
     * @param string $filePath  Absolute path to the file
     * @return string|null      Extracted text, or null on failure
     */
    public function extractText(string $filePath): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Vision API key not configured');
            return null;
        }

        if (!file_exists($filePath)) {
            Log::error('Google Vision: file not found: ' . $filePath);
            return null;
        }

        $mimeType = mime_content_type($filePath);
        $isPdf    = in_array($mimeType, ['application/pdf', 'image/tiff']);

        Log::info('Google Vision: processing file', [
            'path'     => $filePath,
            'mimeType' => $mimeType,
            'isPdf'    => $isPdf,
        ]);

        return $isPdf
            ? $this->extractTextFromPdf($filePath, $mimeType)
            : $this->extractTextFromImage($filePath);
    }

    /**
     * Extract text from an image file using images:annotate.
     */
    private function extractTextFromImage(string $filePath): ?string
    {
        try {
            $content = base64_encode(file_get_contents($filePath));

            $payload = [
                'requests' => [
                    [
                        'image'        => ['content' => $content],
                        'features'     => [['type' => 'DOCUMENT_TEXT_DETECTION', 'maxResults' => 1]],
                        'imageContext' => ['languageHints' => ['id', 'en']],
                    ],
                ],
            ];

            $response = $this->postWithRetry(
                $this->imageEndpoint . '?key=' . $this->apiKey,
                $payload,
                timeout: 30
            );

            if (!$response || !$response->successful()) {
                Log::error('Google Vision image API error', [
                    'status' => $response ? $response->status() : 'no response',
                    'body'   => $response ? substr($response->body(), 0, 500) : 'all retries failed',
                ]);
                return null;
            }

            $json = $response->json();
            return $this->parseImageResponse($json);

        } catch (\Exception $e) {
            Log::error('Google Vision image exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract text from a PDF/TIFF file using files:annotate.
     * This endpoint supports multi-page documents.
     */
    private function extractTextFromPdf(string $filePath, string $mimeType): ?string
    {
        try {
            $content = base64_encode(file_get_contents($filePath));

            $payload = [
                'requests' => [
                    [
                        'inputConfig' => [
                            'content'   => $content,
                            'mimeType'  => $mimeType,
                        ],
                        'features' => [
                            ['type' => 'DOCUMENT_TEXT_DETECTION'],
                        ],
                        'imageContext' => [
                            'languageHints' => ['id', 'en'],
                        ],
                        // Process first 2 pages (passport data is usually on page 1)
                        'pages' => [1, 2],
                    ],
                ],
            ];

            $response = $this->postWithRetry(
                $this->fileEndpoint . '?key=' . $this->apiKey,
                $payload,
                timeout: 90
            );

            if (!$response || !$response->successful()) {
                Log::error('Google Vision PDF API error', [
                    'status' => $response ? $response->status() : 'no response',
                    'body'   => $response ? substr($response->body(), 0, 500) : 'all retries failed',
                ]);
                return null;
            }

            $json = $response->json();
            return $this->parsePdfResponse($json);

        } catch (\Exception $e) {
            Log::error('Google Vision PDF exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse response from images:annotate endpoint.
     */
    private function parseImageResponse(array $json): ?string
    {
        $text = $json['responses'][0]['fullTextAnnotation']['text'] ?? null;
        if ($text) {
            Log::info('Google Vision image: text extracted', ['length' => strlen($text)]);
            return $text;
        }

        $annotations = $json['responses'][0]['textAnnotations'] ?? [];
        if (!empty($annotations)) {
            $text = $annotations[0]['description'] ?? '';
            Log::info('Google Vision image: text from annotations', ['length' => strlen($text)]);
            return $text ?: null;
        }

        Log::warning('Google Vision image: no text found in response');
        return null;
    }

    /**
     * Parse response from files:annotate endpoint.
     * Returns text from the page that contains passport/ID data (MRZ or keywords).
     * Falls back to concatenating all pages if no specific page is identified.
     */
    private function parsePdfResponse(array $json): ?string
    {
        $responses = $json['responses'][0]['responses'] ?? [];

        if (empty($responses)) {
            Log::warning('Google Vision PDF: no page responses found');
            return null;
        }

        // Try to find the page with passport/ID data (contains MRZ or passport keywords)
        $passportPage = '';
        $allText = '';
        foreach ($responses as $idx => $pageResponse) {
            $pageText = $pageResponse['fullTextAnnotation']['text'] ?? '';
            if ($pageText) {
                $allText .= $pageText . "\n";
                // Check if this page contains MRZ or passport data keywords
                if (preg_match('/P<[A-Z]{3}|PASSPORT\s*NO|NO\.?\s*PASPOR|NAMA\s*LENGKAP|FULL\s*NAME/i', $pageText)) {
                    $passportPage .= $pageText . "\n";
                }
            }
        }

        // Prefer passport-specific page if found
        $result = trim($passportPage) ?: trim($allText);

        if ($result) {
            Log::info('Google Vision PDF: text extracted', [
                'pages'  => count($responses),
                'length' => strlen($result),
                'usedPassportPage' => !empty(trim($passportPage)),
                'preview' => substr($result, 0, 200),
            ]);
            return $result;
        }

        Log::warning('Google Vision PDF: no text found across all pages');
        return null;
    }

    /**
     * POST request with retry logic for handling DNS/connection timeouts.
     * Retries up to 2 times with increasing connect timeout.
     */
    private function postWithRetry(string $url, array $payload, int $timeout = 60, int $maxRetries = 2): ?\Illuminate\Http\Client\Response
    {
        $lastException = null;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                // Increase connect timeout on each retry
                $connectTimeout = 15 + ($attempt * 10); // 15s, 25s, 35s

                $response = Http::connectTimeout($connectTimeout)
                    ->timeout($timeout)
                    ->post($url, $payload);

                return $response;

            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning("Google Vision request attempt " . ($attempt + 1) . " failed: " . $e->getMessage());

                if ($attempt < $maxRetries) {
                    // Wait before retry (1s, 2s)
                    sleep($attempt + 1);
                }
            }
        }

        Log::error('Google Vision: all retry attempts failed', [
            'lastError' => $lastException ? $lastException->getMessage() : 'unknown',
        ]);

        return null;
    }

    /**
     * Check if the service is configured and available.
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }
}
