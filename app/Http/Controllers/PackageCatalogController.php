<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use Illuminate\Http\Request;
use App\Traits\HasOutletFilter;

class PackageCatalogController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.catalog.view')->only(['index', 'show']);
    }
    
    /**
     * Display public catalog of travel packages
     */
    public function index(Request $request)
    {
        $query = TravelPackage::query()
            ->with(['outlet', 'flightDeparture', 'hotelMakkah', 'hotelMadinah']) // Load relationships for display
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->upcoming();

        // Filter by destination (package type)
        if ($request->filled('destination')) {
            $query->where('package_type', $request->destination);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('departure_date', $request->month);
        }

        // Filter by duration
        if ($request->filled('duration_min')) {
            $query->where('duration_days', '>=', $request->duration_min);
        }
        if ($request->filled('duration_max')) {
            $query->where('duration_days', '<=', $request->duration_max);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'departure_date');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->popular();
                break;
            case 'duration':
                $query->orderBy('duration_days', 'asc');
                break;
            default:
                $query->orderBy('departure_date', 'asc');
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

        // Increment view count
        $package->incrementViewCount();

        // Get available seats
        $availableSeats = $package->getAvailableSeats();

        // Get inclusions as array
        $inclusions = $package->getInclusionsArray();

        return view('admin.travel.catalog.show', compact('package', 'availableSeats', 'inclusions'));
    }

    /**
     * Get packages data for API/AJAX
     */
    public function getData(Request $request)
    {
        $query = TravelPackage::query()
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->upcoming();

        // Apply filters
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
                'id' => $package->id,
                'package_code' => $package->package_code,
                'package_name' => $package->package_name,
                'package_type' => $package->package_type,
                'duration_days' => $package->duration_days,
                'departure_date' => $package->departure_date->format('Y-m-d'),
                'price' => $package->price,
                'available_seats' => $package->getAvailableSeats(),
                'capacity' => $package->capacity,
                'image_url' => $package->getImageUrl(),
                'popularity_score' => $package->getPopularityScore(),
                'status' => $package->status,
            ];
        });

        return response()->json($packages);
    }
}
