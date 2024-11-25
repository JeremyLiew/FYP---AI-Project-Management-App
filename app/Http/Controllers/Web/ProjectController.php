<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $projectsQuery = Project::query();

        if ($searchQuery) {
            $projectsQuery->where('name', 'like', '%' . $searchQuery . '%');
        }

        if ($statusFilter !== 'All') {
            $projectsQuery->where('status', $statusFilter);
        }

        $projects = $projectsQuery->paginate($perPage);

        return response()->json([
                'projects' => $projects,'total' => $projects->total()
            ]
        );
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
            // Retrieve the project role based on the pivot project_role_id
            $projectRole = ProjectRole::find($user->pivot->project_role_id);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role_id' => $user->pivot->project_role_id, // Save the role id for the form
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
            'roles' => $roles,  // Include roles for the select dropdown
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
            'budget_id' => $request->input('budget_id'),
        ]);

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }

        $project->users()->sync($usersWithRoles);

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

        $project->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $usersWithRoles = [];

        foreach ($request->input('members') as $index => $userId) {
            $roleId = $request->input('roles')[$index];
            $usersWithRoles[$userId] = ['project_role_id' => $roleId];
        }

        $project->users()->sync($usersWithRoles);

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
}
