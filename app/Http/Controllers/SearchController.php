<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasOutletFilter;

class SearchController extends Controller
{
    use HasOutletFilter;
    
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
        $this->middleware('permission:travel.search.view')->only(['search', 'autocomplete', 'advanced']);
    }

    /**
     * Perform global search
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->input('q');
        $results = $this->searchService->globalSearch($query, 50);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => $results->count(),
            ]);
        }

        return view('admin.search.results', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    /**
     * Get autocomplete suggestions
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $query = $request->input('q');
        $suggestions = $this->searchService->autocomplete($query, 10);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Filter packages
     */
    public function filterPackages(Request $request)
    {
        $filters = $request->only([
            'departure_date_from',
            'departure_date_to',
            'destination',
            'package_type',
            'status',
            'workflow_stage',
        ]);

        $packages = $this->searchService->filterPackages($filters)
            ->with(['hppCalculation'])
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'packages' => $packages,
            ]);
        }

        return view('admin.travel.package.index', [
            'packages' => $packages,
            'filters' => $filters,
        ]);
    }

    /**
     * Filter jamaah
     */
    public function filterJamaah(Request $request)
    {
        $filters = $request->only([
            'keberangkatan_id',
            'payment_status',
            'document_status',
            'package_id',
        ]);

        $jamaah = $this->searchService->filterJamaah($filters)
            ->with(['jamaahBookings.travelPackage'])
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'jamaah' => $jamaah,
            ]);
        }

        return view('admin.crm.pelanggan.index', [
            'members' => $jamaah,
            'filters' => $filters,
        ]);
    }

    /**
     * Filter tasks
     */
    public function filterTasks(Request $request)
    {
        $filters = $request->only([
            'team',
            'status',
            'due_date_from',
            'due_date_to',
            'assigned_to_user',
            'package_id',
        ]);

        $tasks = $this->searchService->filterTasks($filters)
            ->with(['travelPackage', 'workflowStage', 'assignedUser'])
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'tasks' => $tasks,
            ]);
        }

        return view('admin.travel.task.index', [
            'tasks' => $tasks,
            'filters' => $filters,
        ]);
    }

    /**
     * Save a filter
     */
    public function saveFilter(Request $request)
    {
        $request->validate([
            'filter_name' => 'required|string|max:255',
            'filter_type' => 'required|in:package,jamaah,task',
            'filter_data' => 'required|array',
        ]);

        $success = $this->searchService->saveFilter(
            Auth::id(),
            $request->input('filter_name'),
            $request->input('filter_type'),
            $request->input('filter_data')
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Filter saved successfully' : 'Failed to save filter',
        ]);
    }

    /**
     * Get saved filters
     */
    public function getSavedFilters(Request $request)
    {
        $filterType = $request->input('filter_type');
        $filters = $this->searchService->getSavedFilters(Auth::id(), $filterType);

        return response()->json([
            'success' => true,
            'filters' => $filters,
        ]);
    }

    /**
     * Delete a saved filter
     */
    public function deleteSavedFilter(Request $request, $id)
    {
        $success = $this->searchService->deleteSavedFilter($id, Auth::id());

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Filter deleted successfully' : 'Failed to delete filter',
        ]);
    }
}
