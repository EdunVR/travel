<?php

namespace App\Http\Controllers;

use App\Models\WorkflowTask;
use App\Models\Team;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\HasOutletFilter;

class TaskController extends Controller
{
    use HasOutletFilter;
    
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('permission:travel.task.view')->only(['index', 'getData', 'myTasks']);
        $this->middleware('permission:travel.task.assign')->only(['assignTask']);
        $this->middleware('permission:travel.task.complete')->only(['completeTask']);
        $this->middleware('permission:travel.task.reassign')->only(['reassignTask']);
    }
    /**
     * Display task dashboard
     */
    public function index(Request $request)
    {
        $teams = Team::active()->get();
        $selectedTeam = $request->get('team');
        
        return view('admin.travel.task.index', compact('teams', 'selectedTeam'));
    }

    /**
     * Get tasks data for DataTables
     */
    public function getData(Request $request)
    {
        $query = WorkflowTask::with([
            'travelPackage',
            'workflowStage',
            'team',
            'assignedUser',
            'completedByUser'
        ]);

        // Filter by team
        if ($request->has('team') && $request->team) {
            $query->forTeam($request->team);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->has('user') && $request->user) {
            $query->forUser($request->user);
        }

        // Filter by overdue
        if ($request->has('overdue') && $request->overdue == '1') {
            $query->overdue();
        }

        return DataTables::of($query)
            ->addColumn('package_name', function ($task) {
                return $task->travelPackage ? $task->travelPackage->package_name : '-';
            })
            ->addColumn('stage_name', function ($task) {
                return $task->workflowStage ? $task->workflowStage->stage_name : '-';
            })
            ->addColumn('team_name', function ($task) {
                return $task->team ? $task->team->team_name : '-';
            })
            ->addColumn('assigned_user_name', function ($task) {
                return $task->assignedUser ? $task->assignedUser->name : 'Unassigned';
            })
            ->addColumn('status_badge', function ($task) {
                $badges = [
                    'pending' => '<span class="badge badge-warning">Pending</span>',
                    'in_progress' => '<span class="badge badge-info">In Progress</span>',
                    'completed' => '<span class="badge badge-success">Completed</span>',
                    'cancelled' => '<span class="badge badge-secondary">Cancelled</span>',
                ];
                return $badges[$task->status] ?? $task->status;
            })
            ->addColumn('overdue_indicator', function ($task) {
                if ($task->isOverdue()) {
                    return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</span>';
                }
                return '';
            })
            ->addColumn('action', function ($task) {
                $actions = '<div class="btn-group">';
                
                // View button
                if (auth()->user()->hasPermission('travel.package.view')) {
                    $actions .= '<a href="' . route('travel.packages.show', $task->id_travel_package) . '" 
                                class="btn btn-sm btn-info" title="View Package">
                                <i class="fas fa-eye"></i>
                            </a>';
                }
                
                // Complete button (only for pending/in_progress tasks)
                if (in_array($task->status, ['pending', 'in_progress']) && auth()->user()->hasPermission('travel.task.complete')) {
                    $actions .= '<button type="button" 
                                class="btn btn-sm btn-success complete-task" 
                                data-id="' . $task->id . '"
                                title="Complete Task">
                                <i class="fas fa-check"></i>
                            </button>';
                }
                
                // Reassign button
                if (auth()->user()->hasPermission('travel.task.reassign')) {
                    $actions .= '<button type="button" 
                                class="btn btn-sm btn-warning reassign-task" 
                                data-id="' . $task->id . '"
                                title="Reassign Task">
                                <i class="fas fa-user-edit"></i>
                            </button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'overdue_indicator', 'action'])
            ->make(true);
    }

    /**
     * Get tasks for current user
     */
    public function myTasks(Request $request)
    {
        $userId = Auth::id();
        
        return view('admin.travel.task.my-tasks', compact('userId'));
    }

    /**
     * Complete a task
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $task = WorkflowTask::findOrFail($id);
        
        // Check if task is already completed
        if ($task->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Task is already completed'
            ], 400);
        }

        $task->markAsCompleted(Auth::id(), $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Task completed successfully'
        ]);
    }

    /**
     * Reassign a task to another user
     */
    public function reassign(Request $request, $id)
    {
        $request->validate([
            'assigned_to_user' => 'required|exists:users,id'
        ]);

        $task = WorkflowTask::findOrFail($id);
        
        $task->assigned_to_user = $request->assigned_to_user;
        $task->save();

        // Send notification to newly assigned user
        $this->notificationService->notifyTaskAssigned($request->assigned_to_user, $task);

        return response()->json([
            'success' => true,
            'message' => 'Task reassigned successfully'
        ]);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $task = WorkflowTask::findOrFail($id);
        
        $task->status = $request->status;
        
        if ($request->status === 'completed') {
            $task->completed_at = now();
            $task->completed_by = Auth::id();
        }
        
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully'
        ]);
    }

    /**
     * Get team members for reassignment
     */
    public function getTeamMembers($teamCode)
    {
        $team = Team::where('team_code', $teamCode)->first();
        
        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        }

        $members = $team->members()->select('id', 'name', 'email')->get();

        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Get overdue tasks count
     */
    public function overdueCount(Request $request)
    {
        $query = WorkflowTask::overdue();

        // Filter by team if provided
        if ($request->has('team') && $request->team) {
            $query->forTeam($request->team);
        }

        // Filter by user if provided
        if ($request->has('user') && $request->user) {
            $query->forUser($request->user);
        }

        $count = $query->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
