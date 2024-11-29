<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotificationMapping;

class NotificationController extends Controller
{
    public function unreadCount()
    {
        $userId = Auth::id();
        $unreadCount = UserNotificationMapping::where('user_id', $userId)
            ->where('read_status', false)
            ->count();

        return response()->json(['unreadCount' => $unreadCount]);
    }
}
