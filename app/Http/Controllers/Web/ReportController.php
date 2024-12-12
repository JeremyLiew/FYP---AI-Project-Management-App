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
use App\Mail\ProjectReportMail;
use Illuminate\Support\Facades\Mail;
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
        $userId = $request->input('userId');
        
        // Fetch projects associated with the user through UserProjectMapping
        $projects = Project::whereHas('userProjectMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('tasks')->get();
        
        $chartData = [];
        
        // Loop through each project
        foreach ($projects as $project) {
            $projectId = $project->id;
    
            // Fetch all tasks related to the current project
            $tasks = Task::where('project_id', $projectId)->get();
    
            // Loop through each task to get the related expenses
            foreach ($tasks as $task) {
                // Fetch expenses related to the current task
                $expenses = Expense::where('task_id', $task->id)
                    ->select('name as expense_name', 'amount as expense_value') // Fetch only expense name and amount
                    ->get();
    
                // Add each expense related to the task to chart data
                foreach ($expenses as $expense) {
                    $chartData[] = [
                        'expense_name' => $expense->expense_name, // Expense name
                        'expense_value' => $expense->expense_value, // Expense value
                    ];
                }
            }
        }
        
        return response()->json($chartData); // Return the data in JSON format
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
        if (!in_array($format, ['txt', 'pdf', 'csv'])) {
            return response()->json(['error' => 'Unsupported format'], 400);
        }
    
        $project = Project::findOrFail($id);
    
        // Prepare the content to be downloaded
        $content = $this->generateHumanReadableContent($id);
    
        if ($format === 'txt') {
            return response()->download($content, 'project_' . $project->name . '_details.txt')->deleteFileAfterSend(true);
        } elseif ($format === 'csv') {

            $project = Project::with(['budget'])->findOrFail($id);

            // Fetch tasks by project ID
            $tasks = Task::where('project_id', $id)->get();
        
            // Fetch expenses by project ID
            $expenses = Expense::where('project_id', $id)->get();

            // Fetch expense categories based on expense IDs
            $expenseCategoryIds = $expenses->pluck('expense_category_id');
            $expenseCategories = ExpenseCategory::whereIn('id', $expenseCategoryIds)->get();

            // Generate CSV content
            $csvFileName = 'project_' . $project->name . '_details.csv';
            $filePath = storage_path('app/' . $csvFileName);
    
            $file = fopen($filePath, 'w');
    
            // Add headers to the CSV file
            fputcsv($file, ['Field', 'Value']);
    
            // Add Project Details
            fputcsv($file, ['Project']);
            fputcsv($file, ['Project Name', $project->name]);
            fputcsv($file, ['Description', $project->description ?? 'N/A']);
            fputcsv($file, ['Start Date', $project->start_date ?? 'N/A']);
            fputcsv($file, ['End Date', $project->end_date ?? 'N/A']);
            fputcsv($file, ['Status', $project->status]);
            fputcsv($file, ['Priority', $project->priority ?? 'N/A']);
            fputcsv($file, ['----------------']);
    
            // Add Budget Details
            fputcsv($file, ['Budget']);
            fputcsv($file, ['Budget Name', $project->budget ? $project->budget->name : 'No budget assigned']);
            fputcsv($file, ['Total Budget', $project->budget ? $project->budget->total_budget : 'N/A']);
            fputcsv($file, ['Remaining Amount', $project->budget ? $project->budget->remaining_amount : 'N/A']);
            fputcsv($file, ['----------------']);
    
            // Add Task Details
            if ($tasks->isNotEmpty()) {
                fputcsv($file, ['Tasks']);
                foreach ($tasks as $task) {
                    fputcsv($file, ['Task Name', $task->name]);
                    fputcsv($file, ['Description', $task->description ?? 'N/A']);
                    fputcsv($file, ['Due Date', $task->due_date ?? 'N/A']);
                    fputcsv($file, ['Status', $task->status]);
                    fputcsv($file, ['Priority', $task->priority]);
                    fputcsv($file, ['----------------', '']);
                }
            } else {
                fputcsv($file, ['Tasks', 'No tasks assigned to this project.']);
            }
    
            // Add Expense Details
            if ($expenses->isNotEmpty()) {
                fputcsv($file, ['Expenses']);
                foreach ($expenses as $expense) {
                    fputcsv($file, ['Expense Name', $expense->name]);
                    fputcsv($file, ['Amount', $expense->amount]);
                    fputcsv($file, ['Date Incurred', $expense->date_incurred]);
                    fputcsv($file, ['Description', $expense->description ?? 'N/A']);
                    fputcsv($file, ['Category', $expense->expenseCategory->name ?? 'N/A']);
                    fputcsv($file, ['----------------', '']);
                }
            } else {
                fputcsv($file, ['Expenses', 'No expenses recorded for this project.']);
            }
    
            fclose($file);
    
            // Return the CSV file as a download response
            return response()->download($filePath, $csvFileName)->deleteFileAfterSend(true);
        }
    
        return response()->json(['error' => 'Unsupported format'], 400);
    }

    // Helper function to generate human-readable content
    private function generateHumanReadableContent($id)
    {
        $project = Project::with([ 'budget'])
            ->findOrFail($id);

        // Fetch tasks by project ID
        $tasks = Task::where('project_id', $id)->get();
    
        // Fetch expenses by project ID
        $expenses = Expense::where('project_id', $id)->get();
    
        // Fetch expense categories based on expense IDs
        $expenseCategoryIds = $expenses->pluck('expense_category_id');
        $expenseCategories = ExpenseCategory::whereIn('id', $expenseCategoryIds)->get();
    
        // Start formatting the content for TXT
        $content = "Project Details\n";
        $content .= "================\n";
        $content .= "Project Name: " . $project->name . "\n";
        $content .= "Description: " . ($project->description ?? 'N/A') . "\n";
        $content .= "Start Date: " . ($project->start_date ?? 'N/A') . "\n";
        $content .= "End Date: " . ($project->end_date ?? 'N/A') . "\n";
        $content .= "Status: " . $project->status . "\n";
        $content .= "Priority: " . ($project->priority ?? 'N/A') . "\n\n";
    
        // Add Budget details
        $content .= "\nBudget Details\n";
        $content .= "================\n";
        if ($project->budget) {
            $content .= "Budget Name: " . $project->budget->name . "\n";
            $content .= "Total Budget: " . $project->budget->total_budget . "\n";
            $content .= "Remaining Amount: " . $project->budget->remaining_amount . "\n\n";
        } else {
            $content .= "No budget assigned to this project.\n";
        }
    
        // Add Task details
        $content .= "\nTask Details\n";
        $content .= "================\n";
        if ($tasks->isNotEmpty()) {
            foreach ($tasks as $task) {
                $content .= "Task Name: " . $task->name . "\n";
                $content .= "Description: " . ($task->description ?? 'N/A') . "\n";
                $content .= "Due Date: " . ($task->due_date ?? 'N/A') . "\n";
                $content .= "Status: " . $task->status . "\n";
                $content .= "Priority: " . $task->priority . "\n";
                $content .= "----------------\n";
            }
        } else {
            $content .= "No tasks assigned to this project.\n";
        }
    
        // Add Expense details
        $content .= "\nExpense Details\n";
        $content .= "================\n";
        if ($expenses->isNotEmpty()) {
            foreach ($expenses as $expense) {
                $content .= "Expense Name: " . $expense->name . "\n";
                $content .= "Amount: " . $expense->amount . "\n";
                $content .= "Date Incurred: " . $expense->date_incurred . "\n";
                $content .= "Description: " . ($expense->description ?? 'N/A') . "\n";
                $content .= "Category: " . ($expense->expenseCategory->name ?? 'N/A') . "\n";
                $content .= "----------------\n";
            }
        } else {
            $content .= "No expenses recorded for this project.\n\n";
        }
    
        // Write content to a TXT file
        $filePath = storage_path('app/public/project_' . $project->name . '_details.txt');
        file_put_contents($filePath, $content);
    
        return $filePath;
    }
    
    public function sendProjectReportToUser(Request $request)
    {
        $id = $request->input('projectId');
        
        // Get the format, default to 'txt'
        $format = $request->input('format', 'txt');
        
        $project = Project::findOrFail($id);
        
        // Generate the report
        $filePath = $this->generateHumanReadableContent($id);
    
        // Get the user email, assuming you want to send it to the project's assigned user
        $user = $project->user; // Assuming 'user' is the relation to the user that is assigned to the project
        if (!$user) {
            return response()->json(['error' => 'No user assigned to this project'], 400);
        }
    
        // Send the email with the generated report attached
        Mail::to($user->email)->send(new ProjectDetailsReport($project, $filePath));
    
        return response()->json(['success' => 'Report sent successfully to ' . $user->email]);
    }


}
