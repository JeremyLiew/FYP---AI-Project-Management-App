<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\UserNotificationMapping;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TaskReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tasks = Task::whereNotNull('due_date')
        ->where('due_date', '>=', now()->startOfDay()) // Compare the date part only
        ->where('due_date', '<=', now()->addWeek()->endOfDay()) // Compare the date part only
        ->get();

        foreach ($tasks as $task) {
            // Convert 'due_date' to a Carbon instance
            $dueDate = Carbon::parse($task->due_date);

            // Calculate the days left
            $daysLeft = $dueDate->diffInDays(now());
            if (in_array($daysLeft, [3, 7])) {

                $notification = Notification::create([
                    'message' => "Reminder: Task '{$task->name}' is due in $daysLeft days.",
                ]);

                if ($task->user_id) {
                    UserNotificationMapping::create([
                        'user_id' => $task->user_id,
                        'notification_id' => $notification->id,
                    ]);
                }
            }
        }
    }
}
