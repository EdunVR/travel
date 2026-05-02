<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
            'recent_activities' => UserActivityLog::with('user')
                ->latest()
                ->limit(10)
                ->get()
        ];

        $usersByRole = Role::withCount('users')->get();
        
        return view('admin.dashboard', compact('stats', 'usersByRole'));
    }
}
