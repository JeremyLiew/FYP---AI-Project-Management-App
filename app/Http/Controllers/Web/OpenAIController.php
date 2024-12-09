<?php


namespace App\Http\Controllers\Web;

use App\Models\Task;
use GuzzleHttp\Client;
use App\Models\Project;
use App\Models\AIFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpenAIController extends Controller
{
    public function sendMessage(Request $request)
    {
        $client = new Client();
        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        // Fetch the authenticated user
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Fetch user's role
        $role = $user->applicationRole->name;

        // Fetch user's projects and tasks
        $projects = Project::whereIn('id', function ($query) use ($user) {
            $query->select('project_id')
                ->from('user_project_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'status'])->toArray();

        $tasks = Task::whereIn('id', function ($query) use ($user) {
            $query->select('task_id')
                ->from('user_task_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'status', 'project_id'])->toArray();

        // Create user context for AI
        $context = "User: {$user->name} ({$role}). "
                . "Email: {$user->email}. "
                . "Projects: " . collect($projects)->pluck('name')->join(', ') . ". "
                . "Tasks: " . collect($tasks)->pluck('name')->join(', ') . ".";

        try {
            // Call OpenAI API
            $response = $client->post($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openai_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'assistant', 'content' => $context], // Provide the user's context
                        ['role' => 'user', 'content' => $request->message], // Include the user's actual message
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $gpt_reply = $data['choices'][0]['message']['content'];

            return response()->json(['message' => $gpt_reply]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function getFeedbacks(){
        return response()->json([
            'feedbacks' => AIFeedback::where('user_id', auth()->id())->get(),
        ]);
    }

    public function generateSummaryFeedback()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $client = new Client();

        // OpenAI API key and endpoint
        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        // Fetch the user's role
        $role = $user->applicationRole->name;

        // Fetch user's projects via UserProjectMapping
        $projects = Project::whereIn('id', function ($query) use ($user) {
            $query->select('project_id')
                ->from('user_project_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'status'])->toArray();

        // Fetch user's tasks via UserTaskMapping
        $tasks = Task::whereIn('id', function ($query) use ($user) {
            $query->select('task_id')
                ->from('user_task_mappings')
                ->where('user_id', $user->id);
        })->get(['id', 'name', 'status', 'project_id'])->toArray();

        // Prepare detailed data for GPT
        $projectDetails = collect($projects)->map(function ($project) {
            return "{$project['name']} (Status: {$project['status']})";
        })->join('; ');

        $taskDetails = collect($tasks)->map(function ($task) use ($projects) {
            $projectName = collect($projects)->firstWhere('id', $task['project_id'])['name'] ?? 'Unknown Project';
            return "{$task['name']} (Status: {$task['status']}, Project: $projectName)";
        })->join('; ');

        $summary = "The user is {$user->name}, who is a {$role}. "
            . "They have " . count($projects) . " projects: {$projectDetails}. "
            . "They are handling " . count($tasks) . " tasks: {$taskDetails}.";

        try {
            // Call GPT API
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

            // Save feedback to `ai_feedbacks` table
            AIFeedback::create([
                'user_id' => $user->id,
                'ai_model' => 'gpt-3.5-turbo',
                'feedback' => $gpt_reply,
                'rating' => 0,
                'feedbackable_type' => 'App\Models\User',
                'feedbackable_id' => $user->id,
            ]);

            // Return success response
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

        // Fetch authenticated user
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Fetch project and its users with roles
        $project = Project::with(['userProjectMappings.user', 'userProjectMappings.projectRole'])->find($projectId);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        // Extract project description
        $projectDescription = $project->description;

        // Extract members with their roles
        $members = $project->userProjectMappings->map(function ($mapping) {
            return [
                'name' => $mapping->user->name,
                'description' => $mapping->user->description ?? 'No description provided',
                'role_name' => $mapping->projectRole->name ?? 'No role assigned',
                'role_description' => $mapping->projectRole->description ?? 'No description provided',
            ];
        });

        // Prepare AI prompt
        $prompt = "
        Analyze the following project and its members:

        Project:
        - Name: {$project->name}
        - Description: {$projectDescription}

        Members:
        " . $members->map(function ($member) {
            return "- {$member['name']} ({$member['role_name']}): {$member['description']}. Role Details: {$member['role_description']}";
        })->join("\n") . "

        Provide actionable insights for:
        1. How to approach this project based on its description.
        2. Roles or tasks suitable for each member based on their descriptions and assigned roles.
        3. Any potential challenges or opportunities for the team.";

        try {
            // Call OpenAI API
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

            // Return the feedback message for display on UI
            return response()->json([
                'message' => 'Project insights generated successfully!',
                'gpt_reply' => $gpt_reply,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!', 'details' => $e->getMessage()], 500);
        }
    }
}
