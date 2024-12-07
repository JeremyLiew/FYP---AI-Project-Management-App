<?php

namespace App\Http\Controllers\Web;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\Task;
use App\Models\Budget;
use App\Models\UserProjectMapping;
use App\Models\UserTaskMapping;
use App\Models\AIFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportController extends Controller
{

    public function getProjectsAndTasks(Request $request)
    {
        $userId = $request->input('userId');
    
        // Fetch projects associated with the user through UserProjectMapping
        $projects = Project::whereHas('userProjectMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('tasks')->get();
    
        // Fetch tasks associated with the user through UserTaskMapping
        $tasks = Task::whereHas('userTaskMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    
        return response()->json([
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
    
    public function getExpenseCategoryData(Request $request)
    {
        // Fetch expense categories
        $categories = ExpenseCategory::all();

        // Prepare data for chart
        $chartData = [];
        foreach ($categories as $category) {
            $expenses = Expense::where('expense_category_id', $category->id)
                ->selectRaw('DATE(date_incurred) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            $chartData[] = [
                'category' => $category->name,
                'data' => $expenses->map(function ($expense) {
                    return [
                        'date' => $expense->date,
                        'total' => $expense->total,
                    ];
                })->toArray(),
            ];
        }

        return response()->json($chartData);
    }

    public function getPerformanceData(Request $request)
    {
        $userId = $request->input('userId');
        
        // Fetch projects associated with the user through UserProjectMapping
        $projects = Project::whereHas('userProjectMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('tasks')->get();
    
        // Fetch all tasks associated with the user through UserTaskMapping
        $tasks = Task::whereHas('userTaskMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    
        // Calculate project stats
        $projectStats = $projects->map(function ($project) {
            $rating = '';
    
            if ($project->status == 'Completed') {
                // Check the difference between end_date and updated_at
                $endDate = \Carbon\Carbon::parse($project->end_date);
                $updatedAt = \Carbon\Carbon::parse($project->updated_at);
                $diffInDays = $endDate->diffInDays($updatedAt);
    
                if ($diffInDays <= 3) {
                    $rating = 'Very Good';
                } elseif ($diffInDays <= 6) {
                    $rating = 'Good';
                } elseif ($diffInDays == 0) {
                    $rating = 'Well';
                } elseif ($diffInDays > 3) {
                    $rating = 'Bad';
                } elseif ($diffInDays > 6) {
                    $rating = 'Poor';
                }
            } elseif ($project->status == 'Ongoing' || $project->status == 'Pending') {
                $rating = 'In Progress';
            }
    
            return [
                'name' => $project->name,
                'status' => $project->status,
                'completionRate' => $rating,
            ];
        });
    
        // Calculate task stats
        $taskStats = $tasks->map(function ($task) {
            $isCompletedOnTime = $task->due_date && $task->due_date <= now()->subDays(3);
            return [
                'name' => $task->name,
                'status' => $task->status,
                'completionRate' => $isCompletedOnTime ? 'Good' : 'Poor',
            ];
        });
    
        return response()->json([
            'projects' => $projectStats,
            'tasks' => $taskStats,
        ]);
    }
    
    public function fetchAiFeedback(Request $request)
    {
        $userId = $request->input('userId');
    
        $feedback = AIFeedback::where('user_id', $userId)->latest()->first();
    
        return response()->json($feedback);
    }

    public function fetchExpensesByProjectId($projectId)
    {
        // Fetch all expenses related to the project
        $expenses = Expense::where('project_id', $projectId)
            ->orderBy('date_incurred', 'asc') // Sort by date
            ->get();

        return response()->json($expenses);
    }

    public function fetchProjectTasks($projectId)
    {
        // Retrieve all tasks for the given project ID
        $tasks = Task::where('project_id', $projectId)
                     ->get();

        return response()->json($tasks);
    }
}
