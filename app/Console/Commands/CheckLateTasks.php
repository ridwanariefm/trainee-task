<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TraineeTask;
use Carbon\Carbon;

class CheckLateTasks extends Command
{
    protected $signature = 'tasks:check-late';

    protected $description = 'Update status tugas menjadi Late jika melewati deadline';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        $affectedRows = TraineeTask::where('deadline', '<', $today)
                        ->where('status', 'Progress')
                        ->update(['status' => 'Late']);

        $this->info("Berhasil update status {$affectedRows} tugas menjadi Late.");
    }
}
