<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\Notification;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\CreateProjectRequest;
use App\Http\Requests\Web\UpdateProjectRequest;
use App\Http\Requests\Web\GetProjectListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{
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

        return response()->json([
            'project' => $project,
            'members' => $members,
            'roles' => $roles,
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
            $fileName = $attachment->getClientOriginalName();  // Get original file name
            $fileType = $attachment->getClientMimeType();     // Get file type (MIME type)
            $filePath = $attachment->store('attachments');    // Store the file and get its path
            
            // Optional: You can also use storeAs() if you want to specify the filename
            // $filePath = $attachment->storeAs('attachments', $fileName); // Store with the original file name
            $attachment = Attachment::create([
                'file_type' => $fileType,
                'file_path' => $filePath,
                'project_id' => $project->id,  // Attach the file to the created project
            ]);
        }

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }
    
        // Get the current users before syncing
        $existingUserIds = $project->users->pluck('id')->toArray();
    
        // Sync users with the project (update the pivot table)
        $project->users()->sync($usersWithRoles);
    
        // Get the new members after syncing
        $newUserIds = array_keys($usersWithRoles);
    
        // Determine the newly added users
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
    
        // Return response
        return response()->json([
            'message' => 'Project created successfully.',
            'project' => $project
        ]);
    }


    public function updateAttachment(Request $request)
    {
        // Validate input
        $request->validate([
            'file' => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048', // Validate file type and size
            'project_id' => 'required|integer', // Assuming project_id is mandatory
        ]);

        // Find the existing attachment by project_id
        $attachment = Attachment::where('project_id', $request->input('project_id'))->first();

        if (!$attachment) {
            return response()->json(['error' => 'Attachment not found for the given project'], 404); // Return error if attachment doesn't exist
        }

        // Check if a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Get the original name
            $fileName = $file->getClientOriginalName();

            // Get the file MIME type
            $fileType = $file->getMimeType();

            // Store the file and get the path
            $filePath = $file->store('attachments', 'public');

            // Update the attachment using update() method
            $attachment->update([
                'file_path' => $filePath,
                'attachmentable_type' => $fileType, // Update the file type (you can change this if needed)
                'project_id' => $request->input('project_id'), // You can change project_id if necessary
            ]);
        }

        // Return response with the updated attachment details
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

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => $project
        ]);
    }

    public function deleteProject($id)
    {
        try {
            $project = Project::findOrFail($id);
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

}
