@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">

    <div class="container">
        <div class="card">
            <div class="card-header">
                Daftar Tugas Trainee
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">[+] Create</a>

                    @if(Auth::guard('admin')->check())
                        <button type="button" class="btn btn-danger" id="btn-mass-delete" style="display:none;">
                            [Hapus Terpilih]
                        </button>
                    @endif

                    <button type="button" class="btn btn-info" data-toggle="modal"
                        data-target="#filterModal">[Filter]</button>

                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importModal">
                        [Import Excel]
                    </button>

                    @if(Auth::guard('admin')->check())
                        <button type="button" class="btn btn-danger" id="btn-mass-delete" style="display:none;">[Delete
                            Selected]</button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tasks-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%"><input type="checkbox" id="check-all"></th>
                                <th>Nama Trainee</th>
                                <th>Tugas</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="status_task_id">
                    <div class="d-grid gap-2">
                        <button onclick="saveStatus('Progress')" class="btn btn-primary btn-block mb-2">Progress</button>
                        <button onclick="saveStatus('Done')" class="btn btn-success btn-block mb-2">Done</button>
                        <button onclick="saveStatus('Late')" class="btn btn-danger btn-block mb-2">Late</button>
                        <button onclick="saveStatus('Canceled')" class="btn btn-dark btn-block">Canceled</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Data Tugas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="form-group">
                            <label>Status Tugas</label>
                            <select class="form-control" id="filter_status">
                                <option value="">-- Semua Status --</option>
                                <option value="Progress">Progress</option>
                                <option value="Done">Done</option>
                                <option value="Late">Late</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                    <a href="#" id="btn-export" class="btn btn-success mr-auto">Export Excel</a>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-apply-filter">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('tasks.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih File Excel (.xlsx)</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <div class="alert alert-info text-small">
                            <strong>Format Kolom Excel:</strong><br>
                            @if(Auth::guard('admin')->check())
                                email | task | desc | start_date | deadline
                            @else
                                task | desc | start_date | deadline
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Import Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>

        var $j = jQuery.noConflict(true);

        $j(document).ready(function () {

            var table = $j('#tasks-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('tasks.index') }}?type=json",
                    type: 'GET',
                    data: function (d) {

                        d.status = $j('#filter_status').val();
                    }
                },
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false },
                    { data: 'trainee_name' },
                    { data: 'task' },
                    { data: 'start_date' },
                    { data: 'deadline' },
                    {
                        data: 'status',
                        render: function (data, type, row) {
                            var badgeClass = 'secondary';
                            if (data == 'Progress') badgeClass = 'primary';
                            else if (data == 'Done') badgeClass = 'success';
                            else if (data == 'Late') badgeClass = 'danger';
                            else if (data == 'Canceled') badgeClass = 'dark';

                            return '<span class="badge badge-' + badgeClass + ' status-btn" style="cursor:pointer" data-id="' + row.id + '">' + data + '</span>';
                        }
                    },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $j(document).on('click', '.status-btn', function () {
                var id = $j(this).data('id');
                $j('#status_task_id').val(id);
                $j('#statusModal').modal('show');
            });

            $j('#check-all').click(function () {
                var isChecked = $j(this).prop('checked');
                $j('.check-item').prop('checked', isChecked);
            });

            $j('#tasks-table').on('change', '.check-item, #check-all', function () {
                var checkedCount = $j('.check-item:checked').length;

                if (checkedCount > 0) {
                    $j('#btn-mass-delete').show();
                } else {
                    $j('#btn-mass-delete').hide();
                }
            });

            $j('#btn-mass-delete').click(function () {

                var ids = [];
                $j('.check-item:checked').each(function () {
                    ids.push($j(this).val());
                });

                if (confirm("Yakin ingin menghapus " + ids.length + " data tugas?")) {
                    var token = "{{ csrf_token() }}";

                    $j.ajax({
                        url: "{{ route('tasks.deleteMultiple') }}",
                        type: 'POST',
                        data: {
                            _token: token,
                            ids: ids
                        },
                        success: function (response) {
                            $j('#tasks-table').DataTable().ajax.reload(null, false);
                            $j('#btn-mass-delete').hide();
                            $j('#check-all').prop('checked', false);

                            alert(response.message);
                        },
                        error: function (xhr) {
                            alert('Gagal menghapus data');
                        }
                    });
                }
            });

            $j('#btn-apply-filter').click(function () {
                $j('#filterModal').modal('hide');
                $j('#tasks-table').DataTable().ajax.reload();
            });

            updateExportLink();

            $j('#filter_status').change(function () {
                updateExportLink();
            });

            function updateExportLink() {
                var status = $j('#filter_status').val();
                var baseUrl = "{{ route('tasks.export') }}";

                $j('#btn-export').attr('href', baseUrl + '?status=' + status);
            }
        });

        function saveStatus(newStatus) {
            var id = $j('#status_task_id').val();
            var token = "{{ csrf_token() }}";

            $j.ajax({
                url: "{{ route('tasks.updateStatus') }}",
                type: 'POST',
                data: {
                    _token: token,
                    id: id,
                    status: newStatus
                },
                success: function (response) {
                    $j('#statusModal').modal('hide');
                    $j('#tasks-table').DataTable().ajax.reload(null, false);
                },
                error: function (xhr) {
                    alert('Gagal mengubah status');
                }
            });
        }
    </script>

@endsection