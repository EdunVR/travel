<?php

namespace App\Http\Controllers;

use App\Models\CustomerCommunication;
use App\Models\Member;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\HasOutletFilter;

class CommunicationController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.communication.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:travel.communication.create')->only(['store']);
        $this->middleware('permission:travel.communication.update')->only(['update', 'scheduleFollowUp']);
        $this->middleware('permission:travel.communication.delete')->only(['destroy']);
    }

    /**
     * Display communication log page
     */
    public function index()
    {
        $members = Member::where('is_jamaah', true)->get();
        $packages = TravelPackage::all();
        return view('admin.travel.communication.index', compact('members', 'packages'));
    }

    /**
     * Get communications data for DataTables
     */
    public function getData(Request $request)
    {
        $query = CustomerCommunication::with(['member', 'travelPackage', 'contactedByUser']);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('id_member', $request->member_id);
        }

        if ($request->filled('package_id')) {
            $query->where('id_travel_package', $request->package_id);
        }

        if ($request->filled('communication_method')) {
            $query->where('communication_method', $request->communication_method);
        }

        if ($request->filled('follow_up_status')) {
            $query->where('follow_up_status', $request->follow_up_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('communication_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('communication_date', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('member_name', function($comm) {
                return $comm->member->nama ?? '-';
            })
            ->addColumn('package_name', function($comm) {
                return $comm->travelPackage->package_name ?? '-';
            })
            ->addColumn('contacted_by_name', function($comm) {
                return $comm->contactedByUser->name ?? '-';
            })
            ->addColumn('method_badge', function($comm) {
                $badges = [
                    'phone_call' => 'primary',
                    'whatsapp' => 'success',
                    'email' => 'info',
                    'in_person' => 'warning',
                    'other' => 'secondary'
                ];
                $color = $badges[$comm->communication_method] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . $comm->communication_method_label . '</span>';
            })
            ->addColumn('status_badge', function($comm) {
                $badges = [
                    'pending' => 'warning',
                    'contacted' => 'info',
                    'responded' => 'success',
                    'no_response' => 'danger'
                ];
                $color = $badges[$comm->follow_up_status] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . $comm->follow_up_status_label . '</span>';
            })
            ->addColumn('overdue_indicator', function($comm) {
                if ($comm->isFollowUpOverdue()) {
                    return '<i class="fas fa-exclamation-triangle text-danger" title="Overdue"></i>';
                }
                return '';
            })
            ->addColumn('actions', function($comm) {
                return '
                    <button class="btn btn-sm btn-info view-communication" data-id="' . $comm->id . '">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit-communication" data-id="' . $comm->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-communication" data-id="' . $comm->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['method_badge', 'status_badge', 'overdue_indicator', 'actions'])
            ->make(true);
    }

    /**
     * Store a new communication log
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_member' => 'required|exists:member,id_member',
            'id_travel_package' => 'nullable|exists:travel_packages,id',
            'communication_method' => 'required|in:phone_call,whatsapp,email,in_person,other',
            'communication_date' => 'required|date',
            'notes' => 'nullable|string',
            'follow_up_status' => 'required|in:pending,contacted,responded,no_response',
            'next_follow_up_date' => 'nullable|date|after_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            $validated['contacted_by'] = Auth::id();

            $communication = CustomerCommunication::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Communication logged successfully',
                'data' => $communication
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to log communication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific communication record
     */
    public function show($id)
    {
        $communication = CustomerCommunication::with(['member', 'travelPackage', 'contactedByUser'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $communication
        ]);
    }

    /**
     * Update a communication record
     */
    public function update(Request $request, $id)
    {
        $communication = CustomerCommunication::findOrFail($id);

        $validated = $request->validate([
            'id_member' => 'required|exists:member,id_member',
            'id_travel_package' => 'nullable|exists:travel_packages,id',
            'communication_method' => 'required|in:phone_call,whatsapp,email,in_person,other',
            'communication_date' => 'required|date',
            'notes' => 'nullable|string',
            'follow_up_status' => 'required|in:pending,contacted,responded,no_response',
            'next_follow_up_date' => 'nullable|date|after_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            $communication->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Communication updated successfully',
                'data' => $communication
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update communication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a communication record
     */
    public function destroy($id)
    {
        try {
            $communication = CustomerCommunication::findOrFail($id);
            $communication->delete();

            return response()->json([
                'success' => true,
                'message' => 'Communication deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete communication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get communication history for a specific member
     */
    public function getMemberHistory($memberId)
    {
        $communications = CustomerCommunication::with(['travelPackage', 'contactedByUser'])
            ->byMember($memberId)
            ->chronological()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $communications
        ]);
    }

    /**
     * Get pending follow-ups
     */
    public function getPendingFollowUps()
    {
        $followUps = CustomerCommunication::with(['member', 'travelPackage', 'contactedByUser'])
            ->pendingFollowUps()
            ->orderBy('next_follow_up_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $followUps
        ]);
    }

    /**
     * Calculate response metrics for a member
     */
    public function getResponseMetrics($memberId)
    {
        $communications = CustomerCommunication::byMember($memberId)->get();

        $totalCommunications = $communications->count();
        $responded = $communications->where('follow_up_status', 'responded')->count();
        $noResponse = $communications->where('follow_up_status', 'no_response')->count();
        $pending = $communications->where('follow_up_status', 'pending')->count();

        $responseRate = $totalCommunications > 0 
            ? round(($responded / $totalCommunications) * 100, 2) 
            : 0;

        // Calculate average response time (days between communication and response)
        $responseTimes = [];
        $sortedComms = $communications->sortBy('communication_date');
        
        foreach ($sortedComms as $index => $comm) {
            if ($comm->follow_up_status === 'responded' && $index > 0) {
                $previousComm = $sortedComms[$index - 1];
                $daysDiff = $comm->communication_date->diffInDays($previousComm->communication_date);
                $responseTimes[] = $daysDiff;
            }
        }

        $avgResponseTime = count($responseTimes) > 0 
            ? round(array_sum($responseTimes) / count($responseTimes), 1) 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_communications' => $totalCommunications,
                'responded' => $responded,
                'no_response' => $noResponse,
                'pending' => $pending,
                'response_rate' => $responseRate,
                'avg_response_time_days' => $avgResponseTime
            ]
        ]);
    }

    /**
     * Schedule a follow-up
     */
    public function scheduleFollowUp(Request $request)
    {
        $validated = $request->validate([
            'id_member' => 'required|exists:member,id_member',
            'id_travel_package' => 'nullable|exists:travel_packages,id',
            'next_follow_up_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $validated['communication_method'] = 'other';
            $validated['communication_date'] = now();
            $validated['follow_up_status'] = 'pending';
            $validated['contacted_by'] = Auth::id();

            $communication = CustomerCommunication::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Follow-up scheduled successfully',
                'data' => $communication
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule follow-up: ' . $e->getMessage()
            ], 500);
        }
    }
}
