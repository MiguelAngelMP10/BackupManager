<?php

namespace App\Console\Commands;

use App\Jobs\ProccessTaskJob;
use App\Models\ScheduledTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CheckScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:check';

    /**
     * The console command description.
     */
    protected $description = 'Check and enqueue scheduled tasks based on their cron expressions';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tasks = ScheduledTask::where('enabled', true)->get();

        foreach ($tasks as $task) {
            Log::info("Checking scheduled task {$task->id} " . $task->name);
            if ($task->isDue()) {
                ProccessTaskJob::dispatch($task)->onQueue('default');
                Log::info("Task '{$task->name}' enqueued successfully.");
            }
        }
    }
}
