<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, string $module, string $description, ?array $changes = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id() ?? 1, // Fallback to system user if needed
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
