<?php

namespace App\Exports;

use App\TraineeTask;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TasksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        if (Auth::guard('admin')->check()) {
            $query = TraineeTask::with('trainee')->latest();
        } else {
            $query = TraineeTask::where('trainee_id', Auth::id())->latest();
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Trainee',
            'Judul Tugas',
            'Deskripsi',
            'Tanggal Mulai',
            'Deadline',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($task): array
    {
        return [
            $task->trainee ? $task->trainee->name : '-',
            $task->task,
            $task->desc,
            date('d/m/Y', strtotime($task->start_date)),
            date('d/m/Y', strtotime($task->deadline)),
            $task->status,
            $task->created_at->format('d/m/Y H:i'),
        ];
    }
}