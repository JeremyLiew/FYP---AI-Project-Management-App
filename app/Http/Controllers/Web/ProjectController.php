<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Budget;
use App\Models\Project;
use App\Models\Attachment;
use App\Models\ProjectRole;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\CreateProjectRequest;
use App\Http\Requests\Web\UpdateProjectRequest;

use App\Http\Requests\Web\GetProjectListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'budget' => $project->budget
        ]);
    }

    public function createProject(CreateProjectRequest $request)
    {
        // Create the project
        $project = Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'budget_id' => $request->input('budget_id'),
        ]);

        // Initialize attachment variables
        $fileName = null;
        $fileType = null;
        $filePath = null;

        // Handle the file upload if it exists
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');

            // Get file details
            $fileName = $attachment->getClientOriginalName();
            $fileType = $attachment->getClientMimeType();
            $filePath = $attachment->store('attachments');

            $attachment = Attachment::create([
                'file_type' => $fileType,
                'file_path' => $filePath,
                'project_id' => $project->id,
            ]);
        }

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }

        $existingUserIds = $project->users->pluck('id')->toArray();

        $project->users()->sync($usersWithRoles);

        $newUserIds = array_keys($usersWithRoles);

        $addedUserIds = array_diff($newUserIds, $existingUserIds);

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


    public function updateAttachment(Request $request)
    {
        // Validate input
        $request->validate([
            'file' => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048',
            'project_id' => 'required|integer',
        ]);

        $attachment = Attachment::where('project_id', $request->input('project_id'))->first();

        if (!$attachment) {
            return response()->json(['error' => 'Attachment not found for the given project'], 404);
        }

        // Check if a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = $file->getClientOriginalName();

            $fileType = $file->getMimeType();

            $filePath = $file->store('attachments', 'public');

            $attachment->update([
                'file_path' => $filePath,
                'attachmentable_type' => $fileType,
                'project_id' => $request->input('project_id'),
            ]);
        }

        $this->activityLogger->logActivity('Updated attachment for project', Attachment::class, $attachment->id, [
            'previous' => $previousAttachment,
            'updated' => $attachment->getAttributes()
        ]);

        return response()->json([
            'message' => 'Attachment updated successfully',
            'attachment' => $attachment,
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

            $this->activityLogger->logActivity('Deleted a project', Project::class, $id, null, 'warning');

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

    public function fetchAttachment($projectId)
    {
        $attachment = Attachment::where('project_id', $projectId)->first();

        if (!$attachment) {
            return response()->json(['error' => 'No attachment found for this project'], 404);
        }

        $filePath = storage_path('app/' . $attachment->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->json([
            'file_type' => $attachment->file_type,
            'file_path' => route('download.attachment', ['projectId' => $projectId]),
            'file_name' => basename($attachment->file_path),
        ]);
    }

    public function downloadAttachment($projectId)
    {
        $attachment = Attachment::where('project_id', $projectId)->first();

        if (!$attachment) {
            return response()->json(['error' => 'No attachment found for this project'], 404);
        }

        $filePath = storage_path('app/' . $attachment->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath, basename($attachment->file_path), [
            'Content-Type' => $attachment->file_type,
        ]);
    }

    public function getBudgets(){
        try {
            // Fetch all budgets from the database
            $budgets = Budget::all();

            // Return the budgets in a JSON response
            return response()->json([
                'success' => true,
                'data' => $budgets
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'Error fetching budgets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
