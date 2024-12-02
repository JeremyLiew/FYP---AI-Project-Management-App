<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $action
     * @param string $modelType
     * @param int $modelId
     * @param array|null $changes
     * @param string $logLevel
     */
    public function logActivity(string $action, string $modelType, int $modelId, ?array $changes = null, string $logLevel = 'info')
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'model_id' => $modelId,
            'model_type' => $modelType,
            'ip_address' => Request::ip(),
            'action' => $action,
            'changes' => $changes ? json_encode($changes) : null,
            'log_level' => $logLevel,
        ]);
    }
}
