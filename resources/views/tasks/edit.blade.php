@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Edit Tugas</div>
            <div class="card-body">

                <form action="{{ route('tasks.update', $task->id) }}" method="POST" id="taskForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Trainee</label>
                        @if($isAdmin)
                            <select name="trainee_id" class="form-control">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $task->trainee_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $task->trainee->name ?? '-' }}" disabled>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Judul Tugas</label>
                        <input type="text" name="task" class="form-control" value="{{ old('task', $task->task) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Tugas</label>
                        <textarea name="desc" class="form-control" rows="3">{{ old('desc', $task->desc) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', $task->start_date) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deadline</label>
                                <input type="date" name="deadline" class="form-control"
                                    value="{{ old('deadline', $task->deadline) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="reset" class="btn btn-secondary">Reset</button>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection