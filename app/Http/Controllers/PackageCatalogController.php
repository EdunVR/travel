<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\PackageViewLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\HasOutletFilter;

class PackageCatalogController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:travel.catalog.view')->only(['index', 'show', 'analytics']);
    }

    /**
     * Display public catalog of travel packages
     */
    public function index(Request $request)
    {
        $query = TravelPackage::query()
            ->with(['outlet', 'flightDeparture', 'hotelMakkah', 'hotelMadinah'])
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->upcoming();

        if ($request->filled('destination')) {
            $query->where('package_type', $request->destination);
        }
        if ($request->filled('month')) {
            $query->whereMonth('departure_date', $request->month);
        }
        if ($request->filled('duration_min')) {
            $query->where('duration_days', '>=', $request->duration_min);
        }
        if ($request->filled('duration_max')) {
            $query->where('duration_days', '<=', $request->duration_max);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $sortBy = $request->get('sort_by', 'departure_date');
        switch ($sortBy) {
            case 'price_low':   $query->orderBy('price', 'asc'); break;
            case 'price_high':  $query->orderBy('price', 'desc'); break;
            case 'popular':     $query->popular(); break;
            case 'duration':    $query->orderBy('duration_days', 'asc'); break;
            default:            $query->orderBy('departure_date', 'asc');
        }

        $packages = $query->paginate(12);

        return view('admin.travel.catalog.index', compact('packages'));
    }

    /**
     * Display package detail
     */
    public function show($id)
    {
        $package = TravelPackage::with(['hppCalculation', 'keberangkatan', 'tourPlans.activities'])
            ->findOrFail($id);

        // Log view detail (harian)
        PackageViewLog::logView(
            $package->id,
            request()->headers->get('referer'),
            'admin'
        );

        // Increment total view count di model
        $package->incrementViewCount();

        $availableSeats = $package->getAvailableSeats();
        $inclusions     = $package->getInclusionsArray();

        return view('admin.travel.catalog.show', compact('package', 'availableSeats', 'inclusions'));
    }

    /**
     * Analytics dashboard untuk satu paket
     */
    public function analytics($id)
    {
        $package = TravelPackage::findOrFail($id);

        // ── Data analytics mandiri ─────────────────────────────────────────
        $days = 30;
        $dailyViews = PackageViewLog::getDailyViews($package->id, $days);

        // Statistik ringkasan
        $totalViews7d  = collect($dailyViews)->takeRight(7)->sum('views');
        $totalViews30d = collect($dailyViews)->sum('views');

        // Booking stats
        $totalBookings  = $package->jamaahBookings()->whereNotIn('status', ['cancelled'])->count();
        $conversionRate = $package->view_count > 0
            ? round(($totalBookings / $package->view_count) * 100, 2)
            : 0;

        // Hari dengan views tertinggi
        $peakDay = collect($dailyViews)->sortByDesc('views')->first();

        // ── GA4 Data API (jika dikonfigurasi) ─────────────────────────────
        $ga4Data    = null;
        $ga4Error   = null;
        $ga4Enabled = !empty(config('services.google_analytics.property_id'))
            && file_exists(config('services.google_analytics.credentials_path'));

        if ($ga4Enabled) {
            try {
                $ga4Data = $this->fetchGA4Data($package);
            } catch (\Throwable $e) {
                $ga4Error = $e->getMessage();
                Log::warning('GA4 fetch error: ' . $e->getMessage());
            }
        }

        return view('admin.travel.catalog.analytics', compact(
            'package',
            'dailyViews',
            'totalViews7d',
            'totalViews30d',
            'totalBookings',
            'conversionRate',
            'peakDay',
            'ga4Data',
            'ga4Error',
            'ga4Enabled',
            'days'
        ));
    }

    /**
     * Fetch data dari GA4 Data API menggunakan service account
     */
    private function fetchGA4Data(TravelPackage $package): array
    {
        $credentialsPath = config('services.google_analytics.credentials_path');
        $propertyId      = config('services.google_analytics.property_id');

        if (!file_exists($credentialsPath)) {
            throw new \RuntimeException('GA4 credentials file tidak ditemukan: ' . $credentialsPath);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        $token       = $this->getGA4AccessToken($credentials);

        $endDate   = now()->toDateString();
        $startDate = now()->subDays(29)->toDateString();

        // Query GA4 Data API v1beta
        $body = [
            'dateRanges'  => [['startDate' => $startDate, 'endDate' => $endDate]],
            'dimensions'  => [['name' => 'date'], ['name' => 'pagePath']],
            'metrics'     => [
                ['name' => 'screenPageViews'],
                ['name' => 'totalUsers'],
                ['name' => 'sessions'],
                ['name' => 'bounceRate'],
                ['name' => 'averageSessionDuration'],
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName'    => 'pagePath',
                    'stringFilter' => [
                        'matchType' => 'CONTAINS',
                        'value'     => '/paket/' . $package->id,
                    ],
                ],
            ],
            'orderBys' => [['dimension' => ['dimensionName' => 'date']]],
        ];

        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", $body);

        if (!$response->successful()) {
            throw new \RuntimeException('GA4 API error: ' . $response->body());
        }

        $data = $response->json();

        // Parse rows
        $rows = [];
        foreach ($data['rows'] ?? [] as $row) {
            $rows[] = [
                'date'     => $row['dimensionValues'][0]['value'] ?? '',
                'path'     => $row['dimensionValues'][1]['value'] ?? '',
                'views'    => (int)  ($row['metricValues'][0]['value'] ?? 0),
                'users'    => (int)  ($row['metricValues'][1]['value'] ?? 0),
                'sessions' => (int)  ($row['metricValues'][2]['value'] ?? 0),
                'bounce'   => round((float)($row['metricValues'][3]['value'] ?? 0) * 100, 1),
                'avg_duration' => round((float)($row['metricValues'][4]['value'] ?? 0)),
            ];
        }

        // Totals
        $totals = $data['totals'][0]['metricValues'] ?? [];
        return [
            'rows'         => $rows,
            'total_views'  => (int)  ($totals[0]['value'] ?? 0),
            'total_users'  => (int)  ($totals[1]['value'] ?? 0),
            'total_sessions'=> (int) ($totals[2]['value'] ?? 0),
            'avg_bounce'   => round((float)($totals[3]['value'] ?? 0) * 100, 1),
            'avg_duration' => round((float)($totals[4]['value'] ?? 0)),
            'start_date'   => $startDate,
            'end_date'     => $endDate,
        ];
    }

    /**
     * Dapatkan access token GA4 dari service account credentials menggunakan JWT
     */
    private function getGA4AccessToken(array $credentials): string
    {
        $now = time();
        $exp = $now + 3600;

        $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $exp,
            'iat'   => $now,
        ]));

        $toSign = $header . '.' . $payload;
        openssl_sign($toSign, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');
        $jwt = $toSign . '.' . base64_encode($signature);

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal mendapatkan GA4 token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Get packages data for API/AJAX
     */
    public function getData(Request $request)
    {
        $query = TravelPackage::query()
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->upcoming();

        if ($request->filled('destination')) {
            $query->where('package_type', $request->destination);
        }
        if ($request->filled('duration_min')) {
            $query->where('duration_days', '>=', $request->duration_min);
        }
        if ($request->filled('duration_max')) {
            $query->where('duration_days', '<=', $request->duration_max);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $packages = $query->get()->map(function ($package) {
            return [
                'id'               => $package->id,
                'package_code'     => $package->package_code,
                'package_name'     => $package->package_name,
                'package_type'     => $package->package_type,
                'duration_days'    => $package->duration_days,
                'departure_date'   => $package->departure_date->format('Y-m-d'),
                'price'            => $package->price,
                'available_seats'  => $package->getAvailableSeats(),
                'capacity'         => $package->capacity,
                'image_url'        => $package->getImageUrl(),
                'popularity_score' => $package->getPopularityScore(),
                'status'           => $package->status,
            ];
        });

        return response()->json($packages);
    }
}
