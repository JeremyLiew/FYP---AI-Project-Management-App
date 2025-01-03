<?php


namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Project;
use App\Models\AIFeedback;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\UserTaskMapping;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotificationMapping;

class OpenAIController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function sendMessage(Request $request)
    {
        $client = new Client();
        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $role = $user->applicationRole->name;

        // Fetch user's projects and their tasks
        $projects = Project::with('tasks:id,name,status,project_id')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('project_id')
                    ->from('user_project_mappings')
                    ->where('user_id', $user->id);
            })
            ->get(['id', 'name', 'status']);

        $tasks = $projects->flatMap(function ($project) {
            return $project->tasks->map(function ($task) use ($project) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'status' => $task->status,
                    'project' => $project->name,
                ];
            });
        });

        $context = "User: {$user->name} ({$role}). "
            . "Email: {$user->email}. "
            . "Projects: " . $projects->pluck('name')->join(', ') . ". "
            . "Tasks: " . $tasks->map(fn($t) => "{$t['name']} ({$t['project']})")->join(', ') . ".";

        try {
            $response = $client->post($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openai_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'assistant', 'content' => $context],
                        ['role' => 'user', 'content' => $request->message],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $gpt_reply = $data['choices'][0]['message']['content'];

            $this->activityLogger->logActivity('Sent a message to OpenAI', User::class, 0);

            return response()->json(['message' => $gpt_reply]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'details' => $e->getMessage()], 500);
        }
    }

    public function getFeedbacks()
    {
        $this->activityLogger->logActivity('Fetched feedbacks', AIFeedback::class, 0);

        return response()->json([
            'feedbacks' => AIFeedback::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get(),
        ]);
    }

    public function generateSummaryFeedback()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $client = new Client();

        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $role = $user->applicationRole->name;

        $projects = Project::whereIn('id', function ($query) use ($user) {
            $query->select('project_id')
                ->from('user_project_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'description', 'start_date', 'end_date', 'status', 'priority'])->toArray();

        $tasks = Task::whereIn('id', function ($query) use ($user) {
            $query->select('task_id')
                ->from('user_task_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'description', 'due_date', 'status', 'priority', 'project_id'])->toArray();

        $projectDetails = collect($projects)->map(function ($project) {
            return "Project: {$project['name']}, "
                . "Description: {$project['description']}, "
                . "Start Date: {$project['start_date']}, "
                . "End Date: {$project['end_date']}, "
                . "Status: {$project['status']}, "
                . "Priority: {$project['priority']}";
        })->join("; ");

        $taskDetails = collect($tasks)->map(function ($task) use ($projects) {
            $projectName = collect($projects)->firstWhere('id', $task['project_id'])['name'] ?? 'Unknown Project';
            return "Task: {$task['name']}, "
                . "Description: {$task['description']}, "
                . "Due Date: {$task['due_date']}, "
                . "Status: {$task['status']}, "
                . "Priority: {$task['priority']}, "
                . "Project: $projectName";
        })->join("; ");

        $summary = "The user is {$user->name}, who is a {$role}. "
            . "They have " . count($projects) . " projects: {$projectDetails}. "
            . "They are handling " . count($tasks) . " tasks: {$taskDetails}.";

        try {
            $response = $client->post($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openai_api_key,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant who provides tailored advice.'],
                        ['role' => 'user', 'content' => $summary],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $gpt_reply = $data['choices'][0]['message']['content'];

            AIFeedback::create([
                'user_id' => $user->id,
                'ai_model' => 'gpt-3.5-turbo',
                'feedback' => $gpt_reply,
                'rating' => 0,
                'feedbackable_type' => 'App\Models\User',
                'feedbackable_id' => $user->id,
            ]);

            $this->activityLogger->logActivity('Generated summary feedback', AIFeedback::class, 0);

            return response()->json(['message' => 'Feedback generated successfully!', 'gpt_reply' => $gpt_reply]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function generateProjectInsight($projectId)
    {
        $client = new Client();
        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $project = Project::with(['userProjectMappings.user', 'userProjectMappings.projectRole', 'tasks'])
            ->find($projectId);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $this->activityLogger->logActivity('Generated project insight', Project::class, $projectId);

        $members = $project->userProjectMappings->map(function ($mapping) {
            $userTasks = UserTaskMapping::where('user_id', $mapping->user->id)
                ->with('task')
                ->get()
                ->pluck('task')
                ->map(function ($task) {
                    return "{$task->name} (Status: {$task->status})";
                })
                ->toArray();

            return [
                'name' => $mapping->user->name,
                'description' => $mapping->user->description ?? 'No description provided',
                'role_name' => $mapping->projectRole->name ?? 'No role assigned',
                'role_description' => $mapping->projectRole->description ?? 'No description provided',
                'tasks' => !empty($userTasks) ? implode(', ', $userTasks) : 'No tasks assigned',
            ];
        });

        $tasks = $project->tasks->map(function ($task) {
            return "- Task: {$task->name} (Status: {$task->status}).";
        })->join("\n");

        $prompt = "
        Analyze the following project, its members, and their workloads:

        Project:
        - Name: {$project->name}
        - Description: {$project->description}

        Members and Their Tasks:
        " . $members->map(function ($member) {
            return "- {$member['name']} ({$member['role_name']}): {$member['description']}.
            Role Details: {$member['role_description']}.
            Tasks: {$member['tasks']}";
        })->join("\n") . "

        Project Tasks:
        {$tasks}

        Provide actionable insights in the following structured format:

        1. **Approach to the Project**:
            - Provide a clear, concise plan on how to approach the project, considering its description and goals.

        2. **Roles and Tasks Assignment**:
            - For each member:
                - Current Tasks (List the tasks assigned and their statuses).
                - Suggested Tasks (Suggest new tasks to assign based on roles and workloads).

        3. **Task Distribution Suggestions**:
            - Specify which members are available for new tasks.
            - Provide recommendations for balancing workload among members.

        4. **Potential Challenges and Opportunities**:
            - Highlight any foreseeable challenges and propose strategies to address them.
            - Identify potential opportunities the team can leverage.

        Ensure the response is well-organized, uses numbered sections and bullet points where appropriate, and includes the following additional details:
        - For each suggested task, should be listed with the following format:
            \"- Task: [Task Name] (Status: Suggested) Assigned to: [Assignee Name]\"
        ";

        try {
            $response = $client->post($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openai_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert project management assistant AI.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $gpt_reply = $data['choices'][0]['message']['content'];

            return response()->json([
                'message' => 'Project insights generated successfully!',
                'gpt_reply' => $gpt_reply,
                'suggested_tasks' => $this->extractTaskSuggestions($gpt_reply),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'details' => $e->getMessage()], 500);
        }
    }

    private function extractTaskSuggestions($gptReply)
    {
        $suggestedTasks = [];

        $suggestedTasksPattern = '/Suggested Tasks\s*[:\s]*\n*(.*?)(?=\n{2,}|$)/s';
        preg_match_all($suggestedTasksPattern, $gptReply, $suggestedSections);

        // dd($suggestedSections);

        if (!empty($suggestedSections[1])) {

            foreach ($suggestedSections[1] as $suggestedSection) {

                $taskPattern = '/- Task:\s*(.+?)\s*\(Status:\s*(.+?)\)\s*(?:Assigned to:\s*(.+))?/';
                preg_match_all($taskPattern, $suggestedSection, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $taskName = $match[1];
                    $taskStatus = $match[2];
                    $assignee = isset($match[3]) ? $match[3] : null;

                    $suggestedTasks[] = [
                        'name' => $taskName,
                        'status' => $taskStatus,
                        'assignee' => $assignee,
                    ];
                }
            }
        }

        return $suggestedTasks;
    }

    public function approveTask(Request $request)
    {
        $taskData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assignee_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        $assigneeId = $request->input('assignee_id');;
        $assignedById =  $userId = Auth::id();

        $task = Task::create($taskData);

        $task->users()->sync([
            $assigneeId => ['assigned_by' => $assignedById]
        ]);

        $notification = Notification::create([
            'message' => "Task {$taskData['name']} was assigned to you by " . auth()->user()->name,
        ]);

        UserNotificationMapping::create([
            'user_id' => $assigneeId,
            'notification_id' => $notification->id,
        ]);

        $this->activityLogger->logActivity('Task approved', Task::class, $task->id);

        return response()->json(['message' => 'Task approved and created successfully!', 'task' => $task]);
    }
}
