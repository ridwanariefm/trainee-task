<?php

namespace App\Http\Controllers;

use App\TraineeTask;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Exports\TasksExport;
use App\Imports\TasksImport;
use Maatwebsite\Excel\Facades\Excel;

class TraineeTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->input('type') == 'json') {

        if (Auth::guard('admin')->check()) {
            $query = TraineeTask::with('trainee')->latest();
        } else {
            $query = TraineeTask::where('trainee_id', Auth::id())->latest();
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

            $formattedData = $tasks->map(function($item) {
                return [
                    'id'            => $item->id,
                    'checkbox'      => '<input type="checkbox" name="ids[]" class="check-item" value="'.$item->id.'">',
                    'trainee_name'  => optional($item->trainee)->name ?? '-',
                    'task'          => $item->task,
                    'start_date'    => date('d-m-Y', strtotime($item->start_date)),
                    'deadline'      => date('d-m-Y', strtotime($item->deadline)),
                    'status'        => $item->status,
                    'action'        => '<a href="'.route('tasks.edit', $item->id).'" class="btn btn-sm btn-warning">Edit</a>'
                ];
            });

            return response()->json(['data' => $formattedData]);
        }

        return view('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            $users = User::all();
            $isAdmin = true;
        } else {
            $users = [];
            $isAdmin = false;
        }

        return view('tasks.create', compact('users', 'isAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    $validator = Validator::make($request->all(), [
        'trainee_id' => 'required|exists:users,id',
        'task'       => 'required|string|max:191',
        'start_date' => 'required|date',
        'deadline'   => 'required|date|after_or_equal:start_date',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
    }

    TraineeTask::create([
        'trainee_id' => $request->trainee_id,
        'task'       => $request->task,
        'desc'       => $request->desc,
        'start_date' => $request->start_date,
        'deadline'   => $request->deadline,
    ]);

    return redirect()->route('tasks.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = TraineeTask::findOrFail($id);

        if (!Auth::guard('admin')->check() && $task->trainee_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas ini.');
        }

        if (Auth::guard('admin')->check()) {
            $users = User::all();
            $isAdmin = true;
        } else {
            $users = [];
            $isAdmin = false;
        }

        return view('tasks.edit', compact('task', 'users', 'isAdmin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'task'       => 'required|string|max:191',
            'start_date' => 'required|date',
            'deadline'   => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $task = TraineeTask::findOrFail($id);

        if (Auth::guard('admin')->check() && $request->has('trainee_id')) {
            $task->trainee_id = $request->trainee_id;
        }

        $task->task       = $request->task;
        $task->desc       = $request->desc;
        $task->start_date = $request->start_date;
        $task->deadline   = $request->deadline;


        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:trainee_tasks,id',
            'status' => 'required|in:Progress,Done,Late,Canceled'
        ]);

        $task = TraineeTask::findOrFail($request->id);

        if (!Auth::guard('admin')->check() && $task->trainee_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $task->status = $request->status;
        $task->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui!']);
    }

    public function deleteMultiple(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Hanya admin yang boleh menghapus!'], 403);
        }

        $ids = $request->ids;

        if ($ids && count($ids) > 0) {
            TraineeTask::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus!']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new TasksImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $status = $request->status;

        if (ob_get_length()) ob_end_clean();

        return Excel::download(new TasksExport($status), 'laporan-tugas.xlsx');
    }

        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function destroy($id)
    {
        //
    }
}