<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\GetTaskListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    public function getTasksByProject(GetTaskListingsRequest $request)
    {
        $searchQuery = $request->input('searchQuery', '');
        $statusFilter = $request->input('selectedFilter', 'All');
        $priorityFilter = $request->input('selectedPriority', 'All');

        $query = Task::where('project_id', $request->input('id'));

        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        if ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        if ($priorityFilter !== 'All') {
            $query->where('priority', $priorityFilter);
        }

        $tasks = $query->get();

        return response()->json([
            'tasks' => $tasks
            ]
        );
    }

    public function deleteTask($id){
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json(['message' => 'Task deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete task.'], 500);
        }
    }
}
