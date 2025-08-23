<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK_A\DK_Pool_Task;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $task_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($task_id)
    {
        //
        $this->task_id = $task_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $task = DK_Pool_Task::find($this->task_id);
        if($task)
        {
            $task->is_completed = 1;
            $task->save();
        }
        else return;
    }
}
