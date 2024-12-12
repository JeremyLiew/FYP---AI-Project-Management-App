<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function getUserProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $user->load([
            'applicationRole:id,name',
            'projects:id,name,description',
            'tasks' => function ($query) {
                $query->select('tasks.id', 'tasks.name', 'tasks.status', 'tasks.priority', 'tasks.due_date');
            },
        ]);

        $this->activityLogger->logActivity('Fetched user profile', User::class, $user->id);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->applicationRole->name,
                'profilePicture' => $user->profile_picture,
            ],
            'projects' => $user->projects,
            'tasks' => $user->tasks,
        ]);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        if ($request->hasFile('profile_picture')) {
            $filePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $filePath;
            $user->save();

            $this->activityLogger->logActivity('Updated profile picture', User::class, $user->id);

            return response()->json(['message' => 'Profile picture updated', 'file_path' => $filePath]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function updateUserName(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore(Auth::id()) // Exclude the current user's name from uniqueness check
            ],
        ]);

        $user = Auth::user();
        $previousData = $user->name;
        $user->name = $request->name;
        $user->save();

        $this->activityLogger->logActivity('Updated user name', User::class, $user->id , [
            'previous' => $previousData,
            'updated' => $request->name
        ]);

        return response()->json(['message' => 'Name updated successfully.']);
    }
}
