<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\CreateProjectRequest;
use App\Http\Requests\Web\UpdateProjectRequest;
use App\Http\Requests\Web\GetProjectListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Services\ActivityLogger;

class ProjectController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function getProjectListings(GetProjectListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');
        $statusFilter = $request->input('selectedFilter', 'All');
        $priorityFilter = $request->input('selectedPriority', 'All');

        $projectsQuery = Project::query();

        if ($searchQuery) {
            $projectsQuery->where('name', 'like', '%' . $searchQuery . '%');
        }

        if ($statusFilter !== 'All') {
            $projectsQuery->where('status', $statusFilter);
        }

        if ($priorityFilter !== 'All') {
            $projectsQuery->where('priority', $priorityFilter);
        }

        $projects = $projectsQuery->paginate($perPage);

        $this->activityLogger->logActivity('Viewed project listings', Project::class, 0);

        return response()->json([
                'projects' => $projects,
                'total' => $projects->total()
            ]);
    }

    public function projectInfo($id)
    {
        $project = Project::with(['users' => function ($query) {
            $query->withPivot('project_role_id');
        }])->find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Fetch the members along with their roles
        $members = $project->users->map(function ($user) {
            $projectRole = ProjectRole::find($user->pivot->project_role_id);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role_id' => $user->pivot->project_role_id,
            ];
        });

        // Fetch roles for the project
        $roles = ProjectRole::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });

        $this->activityLogger->logActivity('Viewed project details', Project::class, $id);

        return response()->json([
            'project' => $project,
            'members' => $members,
            'roles' => $roles,
        ]);
    }

    public function createProject(CreateProjectRequest $request)
    {
        $project = Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }

        // Get the current members before syncing
        $existingUserIds = $project->users->pluck('id')->toArray();

        // Sync users with the project
        $project->users()->sync($usersWithRoles);

        // Get the new members after syncing
        $newUserIds = array_keys($usersWithRoles);

        // Determine newly added users
        $addedUserIds = array_diff($newUserIds, $existingUserIds);

        // Send notifications to newly added users only
        foreach ($addedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $notification = Notification::create([
                    'message' => "You have been added to the project '{$project->name}' by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $userId,
                    'notification_id' => $notification->id,
                ]);
            }
        }

        $this->activityLogger->logActivity('Created a new project', Project::class, $project->id);

        return response()->json([
            'message' => 'Project created successfully.',
            'project' => $project
        ]);
    }

    public function updateProject(UpdateProjectRequest $request)
    {
        $project = Project::find($request->input('id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $previousData = $project->getOriginal();

        $project->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }

        // Get the current members before syncing
        $existingUserIds = $project->users->pluck('id')->toArray();

        // Sync users with the project
        $project->users()->sync($usersWithRoles);

        // Get the new members after syncing
        $newUserIds = array_keys($usersWithRoles);

        // Determine newly added and removed users
        $addedUserIds = array_diff($newUserIds, $existingUserIds);
        $removedUserIds = array_diff($existingUserIds, $newUserIds);

        // Notify newly added users
        foreach ($addedUserIds as $userId) {
            $user = User::find($userId); // Ensure user exists
            if ($user) {
                $notification = Notification::create([
                    'message' => "You have been added to the project '{$project->name}' by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $userId,
                    'notification_id' => $notification->id,
                ]);
            }
        }

        // Notify removed users
        foreach ($removedUserIds as $userId) {
            $user = User::find($userId); // Ensure user exists
            if ($user) {
                $notification = Notification::create([
                    'message' => "You have been removed from the project '{$project->name}' by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $userId,
                    'notification_id' => $notification->id,
                ]);
            }
        }

        $this->activityLogger->logActivity('Updated project details', Project::class, $project->id, [
            'previous' => $previousData,
            'updated' => $project->getAttributes()
        ]);

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => $project
        ]);
    }

    public function deleteProject($id)
    {
        try {
            $project = Project::findOrFail($id);

            $this->activityLogger->logActivity('Deleted a project', Project::class, $id);

            $project->delete();

            return response()->json(['message' => 'Project deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Project not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete project.'], 500);
        }
    }

    public function fetchUsersAndRoles()
    {
        $users = User::all();
        $projectRoles = ProjectRole::all();

        return response()->json([
            'users' => $users,
            'projectRoles' => $projectRoles,
        ]);
    }
}
