<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\GetTaskListingsRequest;

class TaskController extends Controller
{
    public function getTasksByProject(GetTaskListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');
        $statusFilter = $request->input('selectedFilter', 'All');

        $query = Task::where('project_id', $request->input('id'));

        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        if ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        $tasks = $query->paginate($perPage);

        return response()->json([
            'tasks' => $tasks,'total' => $tasks->total()
        ]
    );
    }
}
