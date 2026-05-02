<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response Caching Middleware
 * 
 * Cache GET requests untuk mengurangi load database
 * Hanya untuk endpoint yang datanya tidak sering berubah
 */
class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Generate unique cache key based on URL and query parameters
        $cacheKey = $this->generateCacheKey($request);

        // Try to get from cache
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse !== null) {
            // Return cached response with header indicating it's from cache
            return response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders(array_merge($cachedResponse['headers'], [
                    'X-Cache' => 'HIT',
                    'X-Cache-Key' => $cacheKey,
                ]));
        }

        // Process request
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful()) {
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ];

            Cache::put($cacheKey, $cacheData, $ttl);
            
            // Add header indicating cache miss
            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-Key', $cacheKey);
        }

        return $response;
    }

    /**
     * Generate unique cache key for request
     *
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request): string
    {
        $url = $request->url();
        $queryParams = $request->query();
        $userId = auth()->id() ?? 'guest';
        
        // Sort query params for consistent key generation
        ksort($queryParams);
        
        $key = 'response_cache_' . md5($url . json_encode($queryParams) . $userId);
        
        return $key;
    }
}
