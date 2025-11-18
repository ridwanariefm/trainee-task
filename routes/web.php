<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::guard('web')->check() || Auth::guard('admin')->check()) {
        return redirect()->route('tasks.index');
    }
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->group(function() {

    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('auth:admin');
});

Route::group(['middleware' => ['auth:web,admin']], function () {

    Route::get('/admin/dashboard', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::post('/tasks/update-status', 'TraineeTaskController@updateStatus')->name('tasks.updateStatus');
    Route::post('/tasks/delete-multiple', 'TraineeTaskController@deleteMultiple')->name('tasks.deleteMultiple');
    Route::post('/tasks/import', 'TraineeTaskController@import')->name('tasks.import');
    Route::get('/tasks/export', 'TraineeTaskController@export')->name('tasks.export');

    Route::resource('tasks', 'TraineeTaskController');
});
