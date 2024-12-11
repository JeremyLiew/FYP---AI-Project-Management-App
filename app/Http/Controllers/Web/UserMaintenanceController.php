<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ApplicationRole;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserDeletedNotification;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\UpdateUserRequest;

class UserMaintenanceController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function fetchUsers(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');

        $usersQuery = User::with('applicationRole');

        if (!empty($searchQuery)) {
            $usersQuery->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
        }

        $users = $usersQuery->paginate($perPage);

        $this->activityLogger->logActivity('Viewed user listings', User::class, 0);

        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'description' => $user->description,
                'application_role_id' => $user->applicationRole->id ?? 'N/A',
                'profile_picture' => $user->profile_picture,
                'email_verified_at' => $user->email_verified_at
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
        ]);
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $user = User::findOrFail($request->input('id'));
        $previousData = $user->getOriginal();

        $oldRoleId = $user->application_role_id;
        $newRoleId = $request->input('application_role_id');

        if ($oldRoleId != $newRoleId) {
            $oldRole = ApplicationRole::find($oldRoleId)?->name ?? 'No Role';
            $newRole = ApplicationRole::findOrFail($newRoleId)->name;

            $notification = Notification::create([
                'message' => "Your role has been changed from '{$oldRole}' to '{$newRole}' by " . auth()->user()->name,
            ]);

            UserNotificationMapping::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
            ]);
        }

        $user->update($request->validated());

        $this->activityLogger->logActivity('Updated user details', User::class, $user->id, [
            'previous' => $previousData,
            'updated' => $user->getAttributes(),
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $adminName = auth()->user()->name;

        $user->delete();

        Mail::to($user->email)->send(new UserDeletedNotification($user, $adminName));

        $this->activityLogger->logActivity('Deleted user', User::class, $user->id, null, 'warning');
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function fetchApplicationRoles(){
        $roles = ApplicationRole::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });

        $this->activityLogger->logActivity('Fetched application roles', ApplicationRole::class, 0);

        return response()->json($roles);
    }
}
