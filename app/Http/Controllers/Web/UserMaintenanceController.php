<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApplicationRole;

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
                'application_role_id' => $user->applicationRole->id ?? 'N/A', // Using 'applicationRole' relationship
                'profile_picture' => $user->profile_picture,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
        ]);
    }

    public function updateUser(Request $request)
    {
        $user = User::findOrFail($request->input('id'));
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
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
