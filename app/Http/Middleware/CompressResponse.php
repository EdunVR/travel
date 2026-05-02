<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only compress if client accepts gzip and response is compressible
        if ($this->shouldCompress($request, $response)) {
            $this->compressResponse($response);
        }

        return $response;
    }

    /**
     * Determine if response should be compressed
     */
    private function shouldCompress(Request $request, Response $response): bool
    {
        // Check if client accepts gzip
        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (strpos($acceptEncoding, 'gzip') === false) {
            return false;
        }

        // Check if response is already compressed
        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        // Check content type
        $contentType = $response->headers->get('Content-Type', '');
        $compressibleTypes = [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/plain'
        ];

        foreach ($compressibleTypes as $type) {
            if (strpos($contentType, $type) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compress the response content
     */
    private function compressResponse(Response $response): void
    {
        $content = $response->getContent();
        
        // Only compress if content is substantial (> 1KB)
        if (strlen($content) < 1024) {
            return;
        }

        $compressed = gzencode($content, 6); // Compression level 6 (good balance)
        
        if ($compressed !== false) {
            $response->setContent($compressed);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', strlen($compressed));
            $response->headers->set('Vary', 'Accept-Encoding');
        }
    }
}