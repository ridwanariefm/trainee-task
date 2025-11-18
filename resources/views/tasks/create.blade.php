@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Tambah Tugas Baru</div>
            <div class="card-body">

                <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
                    @csrf

                    <div class="form-group">
                        <label>Nama Trainee</label>
                        @if($isAdmin)
                            <select name="trainee_id" class="form-control">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                            <input type="hidden" name="trainee_id" value="{{ Auth::user()->id }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Judul Tugas</label>
                        <input type="text" name="task" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Tugas</label>
                        <textarea name="desc" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deadline</label>
                                <input type="date" name="deadline" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">

                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-dark">
                            &laquo; Kembali
                        </a>

                        <div>
                            <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">Reset Form</button>
                            <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Submit</button>
                        </div>

                        <button type="submit" id="realSubmitBtn" style="display: none;"></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function resetForm() {
            document.getElementById("taskForm").reset();
        }

        function confirmSubmit() {
            var r = confirm("Apakah Anda yakin data sudah benar?");
            if (r == true) {
                document.querySelector('.btn-primary').disabled = true;
                document.querySelector('.btn-secondary').disabled = true;

                document.getElementById("realSubmitBtn").click();
            }
        }
    </script>
@endsection