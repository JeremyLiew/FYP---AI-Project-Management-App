<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationMapping;
use App\Http\Requests\Web\CreateTaskRequest;
use App\Http\Requests\Web\UpdateTaskRequest;
use App\Http\Requests\Web\CreateCommentRequest;
use App\Http\Requests\Web\UpdateCommentRequest;
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
            $changes = $task->users()->sync([
                $assigneeId => ['assigned_by' => $assignedById]
            ]);

            // Notify newly assigned users
            foreach ($changes['attached'] as $newUserId) {
                $notification = Notification::create([
                    'message' => "Task '{$task->name}' was assigned to you by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $newUserId,
                    'notification_id' => $notification->id,
                ]);
            }
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
            $changes = $task->users()->sync([
                $assigneeId => ['assigned_by' => $assignedById],
            ]);

            // Notify newly assigned users
            foreach ($changes['attached'] as $newUserId) {
                $notification = Notification::create([
                    'message' => "Task '{$task->name}' was assigned to you by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $newUserId,
                    'notification_id' => $notification->id,
                ]);
            }

            // Notify removed users
            foreach ($changes['detached'] as $removedUserId) {
                $notification = Notification::create([
                    'message' => "You have been removed from task '{$task->name}' by " . auth()->user()->name,
                ]);

                UserNotificationMapping::create([
                    'user_id' => $removedUserId,
                    'notification_id' => $notification->id,
                ]);
            }
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

    public function getTaskComments($id)
    {
        try {
            $task = Task::findOrFail($id);

            $comments = $task->comments()->with('user')->get()->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'text' => $comment->comment,
                    'author' => $comment->user->name ?? 'Unknown',
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'comments' => $comments,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found.'], 404);
        }
    }


    public function createTaskComment(CreateCommentRequest $request)
    {

        try {
            $comment = Comment::create([
                'task_id' => $request->input('task_id'),
                'comment' => $request->input('comment'),
                'user_id' => $request->input('creator'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully.',
                'comment' => $comment,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create comment.'], 500);
        }
    }

    public function editTaskComment(UpdateCommentRequest $request)
    {

        try {
            $comment = Comment::findOrFail($request->input('comment_id'));
            $comment->update(['comment' => $request->input('comment')]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
                'comment' => $comment,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update comment.'], 500);
        }
    }

    public function deleteTaskComment($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return response()->json(['message' => 'Comment deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete comment.'], 500);
        }
    }
}
