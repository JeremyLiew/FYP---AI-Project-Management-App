<?php

namespace App\Http\Controllers\Web;

use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
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
        })->with('project')->get();
    
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
            $rating = '';
    
            if ($task->project->status == 'Completed') {
                // Check the difference between due_date and updated_at
                $dueDate = \Carbon\Carbon::parse($task->due_date);
                $updatedAt = \Carbon\Carbon::parse($task->updated_at);
                $diffInDays = $dueDate->diffInDays($updatedAt);
    
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
            } elseif ($task->project->status == 'Ongoing' || $task->project->status == 'Pending') {
                $rating = 'In Progress';
            }
    
            return [
                'name' => $task->name,
                'status' => $task->status,
                'completionRate' => $rating,
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
                     ->get()
                     ->map(function ($task) {
                         $completionTime = null;
    
                         // Calculate completion time only if updated_at is available
                         if ($task->updated_at && $task->created_at) {
                             $createdAt = Carbon::parse($task->created_at);
                             $updatedAt = Carbon::parse($task->updated_at);
                             
                             // Calculate completion time in hours 
                             $completionTime = $createdAt->diffInHours($updatedAt, false);
                         }
    
                         // Add the calculated completion time to the task
                         $task->completion_time = $completionTime;
    
                         return $task;
                     });
    
        return response()->json($tasks);
    }    

    public function fetchTaskStatus($projectId)
    {
        // Retrieve all tasks for the given project ID and return only the status
        $tasks = Task::where('project_id', $projectId)
                     ->get(['id', 'status']); // You can choose which fields to return
    
        return response()->json($tasks);
    }
    
    public function downloadProjectDetails(Request $request)
    {
        $id = $request->input('projectId');
        $format = $request->input('format');
    
        // Validate the format
        if (!in_array($format, ['txt', 'pdf', 'docx'])) {
            return response()->json(['error' => 'Unsupported format'], 400);
        }
    
        $project = Project::findOrFail($id);
    
        // Prepare the content to be downloaded
        $content = $this->generateHumanReadableContent($project);
    
        if ($format === 'txt') {
            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="project_' . $id . '_details.txt"');
        } elseif ($format === 'docx') {
            // Create a new Word document using PHPWord
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
    
            // Add content to the Word document
            $section->addText('Project Name: ' . $project->name);
            $section->addText('Project Description: ' . $project->description);
    
    
            // Save the document as DOCX
            $fileName = 'project_' . $id . '_details.docx';
            $filePath = storage_path('app/public/' . $fileName);
            $phpWord->save($filePath);
    
            // Return the DOCX file as a download response
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    
        return response()->json(['error' => 'Unsupported format'], 400);
    }


    // Helper function to generate human-readable content
    private function generateHumanReadableContent($project)
    {
        // Format the content for TXT or HTML (for PDF)
        $content = "Project Name: " . $project->name . "\n";
        $content .= "Project Description: " . $project->description . "\n";
        
        return $content;
    }

}
