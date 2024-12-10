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

class WeatherController extends Controller
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

}
