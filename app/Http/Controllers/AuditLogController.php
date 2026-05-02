<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\HasOutletFilter;

class AuditLogController extends Controller
{
    use HasOutletFilter;
    
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->middleware('permission:travel.audit.view')->only(['index', 'show']);
        $this->middleware('permission:travel.audit.export')->only(['export']);
    }

    /**
     * Display audit log viewer
     */
    public function index(Request $request)
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'action_type' => $request->input('action_type'),
            'model_type' => $request->input('model_type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Remove null filters
        $filters = array_filter($filters, function ($value) {
            return $value !== null;
        });

        $logs = $this->auditService->getFilteredLogs($filters)->paginate(50);
        
        // Get unique action types for filter dropdown
        $actionTypes = \App\Models\AuditLog::select('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->pluck('action_type');

        // Get unique model types for filter dropdown
        $modelTypes = \App\Models\AuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->orderBy('model_type')
            ->pluck('model_type')
            ->map(function ($type) {
                return class_basename($type);
            });

        // Get users for filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('admin.audit.index', compact('logs', 'filters', 'actionTypes', 'modelTypes', 'users'));
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request)
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'action_type' => $request->input('action_type'),
            'model_type' => $request->input('model_type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Remove null filters
        $filters = array_filter($filters, function ($value) {
            return $value !== null;
        });

        $csv = $this->auditService->exportToCsv($filters);

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
