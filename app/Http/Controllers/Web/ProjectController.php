<?php

namespace App\Http\Controllers\Web;

use App\Models\Project;
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
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        return response()->json([
            'project' => $project
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
}
