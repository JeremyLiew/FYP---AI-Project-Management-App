<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CreateTaskRequest;
use App\Http\Requests\Web\UpdateTaskRequest;
use App\Http\Requests\Web\GetTaskListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    public function getTasksByProject(GetTaskListingsRequest $request)
    {
        $searchQuery = $request->input('searchQuery', '');
        $statusFilter = $request->input('selectedFilter', 'All');
        $priorityFilter = $request->input('selectedPriority', 'All');

        $query = Task::with('users')->where('project_id', $request->input('id'));

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

        $tasks->each(function ($task) {
            $task->assigned_to = $task->users->first()->id;
        });

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

    public function createTask(CreateTaskRequest $request){

        $task = Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'project_id' => $request->input('project_id'),
        ]);

        $assigneeId = $request->input('assigned_to');
        $assignedById = $request->input('assigned_by');
        if ($assigneeId && $assignedById) {
            $task->users()->sync([
                $assigneeId => ['assigned_by' => $assignedById]
            ]);
        }

        return response()->json([
            'message' => 'Project created successfully.',
            'task' => $task
        ]);
    }

    public function updateTask(UpdateTaskRequest $request){

        $task = Task::find($request->input('id'));

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'project_id' => $request->input('project_id'),
        ]);

        $assigneeId = $request->input('assigned_to');
        $assignedById = $request->input('assigned_by');
        if ($assigneeId && $assignedById) {
            $task->users()->sync([
                $assigneeId => ['assigned_by' => $assignedById]
            ]);
        }

        return response()->json([
            'message' => 'Project created successfully.',
            'task' => $task
        ]);
    }

    public function fetchMembers($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            $members = $project->users()->get();

            return response()->json([
                'success' => true,
                'members' => $members,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch members.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
