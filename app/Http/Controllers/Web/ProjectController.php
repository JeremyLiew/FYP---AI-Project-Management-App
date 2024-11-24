<?php

namespace App\Http\Controllers\Web;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    public function getProjectListings(Request $request)
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
}
