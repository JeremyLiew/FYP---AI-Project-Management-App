<?php

namespace App\Http\Controllers\Web;

use App\Models\Notification;
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

    public function getNotifications(Request $request)
    {
        $notifications = Notification::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['users' => function($query) {
            $query->where('user_id', auth()->id());
        }])
        ->get()
        ->map(function ($notification) {
            $userNotification = $notification->users->firstWhere('id', auth()->id());
            $notification->is_read = $userNotification ? $userNotification->pivot->read_status : false;
            return $notification;
        });

        return response()->json($notifications);
    }

    // Mark a notification as read
    public function markAsRead($notificationId)
    {
        $userNotification = UserNotificationMapping::where('notification_id', $notificationId)
            ->where('user_id', auth()->id())
            ->first();

        if ($userNotification) {
            $userNotification->update(['read_status' => true]);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    // Mark all notifications as read (optional)
    public function markAllAsRead()
    {
        UserNotificationMapping::where('user_id', auth()->id())
            ->where('read_status', false)
            ->update(['read_status' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
