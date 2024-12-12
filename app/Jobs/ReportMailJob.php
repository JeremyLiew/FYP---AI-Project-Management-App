<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Project;
use App\Mail\ProjectReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Jobs\Dispatchable;

class ReportMailJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::findOrFail($this->userId);
        $report = $this->generateProjectReport($user);
        Mail::to($user->email)->send(new ProjectReportMail($report));
        \Log::info('Report sent to ' . $user->email);
    }

    private function generateProjectReport($user)
    {
        $projects = Project::whereHas('userProjectMappings', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
        return $this->generateHumanReadableContent($projects);
    }

    private function generateHumanReadableContent($projects)
    {
        $content = "Project Report\n";
        $content .= "================\n";

        foreach ($projects as $project) {
            $content .= "Project Name: " . $project->name . "\n";
            $content .= "Description: " . ($project->description ?? 'N/A') . "\n";
            $content .= "Status: " . $project->status . "\n";
            $content .= "Priority: " . ($project->priority ?? 'N/A') . "\n";
            $content .= "Start Date: " . ($project->start_date ?? 'N/A') . "\n";
            $content .= "End Date: " . ($project->end_date ?? 'N/A') . "\n";
            $content .= "----------------\n";
        }

        return $content;
    }
}