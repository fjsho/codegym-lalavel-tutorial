<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskPictureController;
use App\Http\Controllers\TmpPictureController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::resource('projects', ProjectController::class)
    ->middleware(['auth']);

Route::post('/projects/{project}/tasks/storeTmpPicture', [TmpPictureController::class, 'storeTmpPicture'])
    ->name('tasks.storeTmpPicture')
    ->middleware(['auth']);

Route::delete('/projects/{project}/tasks/destroyTmpPicture', [TmpPictureController::class, 'destroyTmpPicture'])
    ->name('tasks.destroyTmpPicture')
    ->middleware(['auth']);

Route::post('projects/{project}/tasks/create', [TaskController::class, 'create'])
    ->middleware(['auth']);

Route::resource('projects/{project}/tasks', TaskController::class)
    ->middleware(['auth']);

Route::resource('projects/{project}/tasks/{task}/task_comments', TaskCommentController::class)
    ->middleware(['auth']);

Route::resource('projects/{project}/tasks/{task}/task_pictures', TaskPictureController::class)
    ->middleware(['auth']);

