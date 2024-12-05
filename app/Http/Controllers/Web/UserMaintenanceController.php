<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ApplicationRole;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserDeletedNotification;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\UpdateUserRequest;

class UserMaintenanceController extends Controller
{
    public function fetchUsers(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');

        // Query users with their application roles
        $usersQuery = User::with('applicationRole');

        if (!empty($searchQuery)) {
            $usersQuery->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
        }

        $users = $usersQuery->paginate($perPage);

        // Format the user data with application role
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'application_role_id' => $user->applicationRole->id ?? 'N/A',
                'profile_picture' => $user->profile_picture,
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
        $oldRoleId = $user->application_role_id; // Capture the old role ID
        $newRoleId = $request->input('application_role_id');

        // Only proceed with notifications if the role has changed
        if ($oldRoleId != $newRoleId) {
            // Get the old and new role names
            $oldRole = ApplicationRole::find($oldRoleId)?->name ?? 'No Role';
            $newRole = ApplicationRole::findOrFail($newRoleId)->name;

            // Create a notification
            $notification = Notification::create([
                'message' => "Your role has been changed from '{$oldRole}' to '{$newRole}' by " . auth()->user()->name,
            ]);

            // Map the notification to the user
            UserNotificationMapping::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
            ]);
        }

        // Update the user with validated data
        $user->update($request->validated());

        return response()->json(['message' => 'User updated successfully']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $adminName = auth()->user()->name;

        // Delete the user
        $user->delete();

        // Send the email notification
        Mail::to($user->email)->send(new UserDeletedNotification($user, $adminName));
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function fetchApplicationRoles(){
        $roles = ApplicationRole::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });

        return response()->json($roles);
    }
}
