<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveDeletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:remove_deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to remove tasks that are deleted 30 days ago.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tasks = Task::onlyTrashed()
            ->where('deleted_at', '<=', Carbon::now()->subMonth())
            ->get()
        ;
        foreach($tasks as $task) {
            $this->info("Deleting Task #" . $task->id . " (". $task->title .") ");
            $task->forceDelete();
        }
        return Command::SUCCESS;
    }
}
