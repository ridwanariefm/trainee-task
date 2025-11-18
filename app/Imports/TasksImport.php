<?php

namespace App\Imports;

use App\TraineeTask;
use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TasksImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        if (Auth::guard('admin')->check()) {

            $user = User::where('email', $row['email'])->first();

            $trainee_id = $user ? $user->id : null;

            if (!$trainee_id) return null;

        } else {
            $trainee_id = Auth::id();
        }

        return new TraineeTask([
            'trainee_id' => $trainee_id,
            'task'       => $row['task'],
            'desc'       => $row['desc'],
            'start_date' => $row['start_date'],
            'deadline'   => $row['deadline'],
            'status'     => 'Progress',
        ]);
    }
}
