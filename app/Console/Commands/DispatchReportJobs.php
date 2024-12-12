<?php

namespace App\Console\Commands;

use App\Jobs\ReportMailJob;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchReportJobs extends Command
{
    protected $signature = 'reports:dispatch';
    protected $description = 'Dispatch report jobs based on user preferences';

    public function handle()
    {
        $now = Carbon::now();

        Setting::where('key', 'email_time')->chunk(100, function ($settings) use ($now) {
            foreach ($settings as $setting) {
                if ($this->shouldSendReport($setting, $now)) {
                    ReportMailJob::dispatch($setting->user_id);
                    Setting::updateOrCreate(
                        ['user_id' => $setting->user_id, 'key' => 'last_report_sent'],
                        ['value' => $now->toDateTimeString()]
                    );
                }
            }
        });

        $this->info('Report jobs dispatched successfully.');
    }

    private function shouldSendReport($setting, $now)
    {
        $lastSentSetting = Setting::where('user_id', $setting->user_id)
                                  ->where('key', 'last_report_sent')
                                  ->first();

        if (!$lastSentSetting) {
            return true;
        }

        $lastSentAt = Carbon::parse($lastSentSetting->value);

        if ($setting->value === 'Weekly') {
            return $lastSentAt->diffInDays($now) >= 7;
        }

        if ($setting->value === 'Monthly') {
            return $lastSentAt->diffInDays($now) >= 30;
        }

        return false;
    }
}