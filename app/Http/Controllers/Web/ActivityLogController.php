<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function getActivityLogs(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $logLevelFilter = $request->input('logLevelFilter', 'All');
        $modelTypeFilter = $request->input('modelTypeFilter', 'All');
        $userFilter = $request->input('userFilter', 'All');

        $activityLogsQuery = ActivityLog::query();

        $activityLogsQuery->with('user');

        // Filter by log level if specified
        if ($logLevelFilter !== 'All') {
            $activityLogsQuery->where('log_level', $logLevelFilter);
        }

        // Filter by model type if specified
        if ($modelTypeFilter !== 'All') {
            $modelTypeFilter = "App\Models\\$modelTypeFilter";
            $activityLogsQuery->where('model_type', $modelTypeFilter);
        }

        // Filter by user if specified
        if ($userFilter != 'All') {
            $activityLogsQuery->where('user_id', $userFilter);
        }

        $activityLogsQuery->orderBy('created_at', 'desc');

        // Pagination and fetching the results
        $activityLogs = $activityLogsQuery->paginate($perPage);


        return response()->json([
            'logs' => $activityLogs,
            'total' => $activityLogs->total(),
        ]);
    }

    public function fetchUsers()
    {
        $users = User::all(['id', 'name']);
        return response()->json($users);
    }
}
