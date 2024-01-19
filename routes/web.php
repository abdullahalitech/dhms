<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('adminDashboard');
    
    //employee routes
    Route::get('employee', [AdminController::class, 'add_employee'])->name('employee');
    Route::post('add-employee', [AdminController::class, 'store_employee']);
    Route::post('ajax/get-employee', [AdminController::class, 'ajax_get_employee']);
    Route::post('ajax/admin/get-employee-for-edit', [AdminController::class, 'ajax_admin_get_employee']);
    Route::post('update-employee', [AdminController::class, 'update_employee']);
    Route::post('ajax/admin/suspend-user', [AdminController::class, 'ajax_admin_suspend_user']);
    Route::post('ajax/admin/resume-user', [AdminController::class, 'ajax_admin_resume_user']);


    //platform routes
    Route::get('platform', [AdminController::class, 'add_platform'])->name('platform');
    Route::post('add-platform', [AdminController::class, 'store_platform']);
    Route::post('ajax/get-platform', [AdminController::class, 'ajax_get_platform']);
    Route::post('ajax/admin/get-platform-for-edit', [AdminController::class, 'ajax_admin_get_platform']);
    Route::post('update-platform', [AdminController::class, 'update_platform']);
    Route::post('ajax/admin/suspend-platform', [AdminController::class, 'ajax_admin_suspend_platform']);
    Route::post('ajax/admin/resume-platform', [AdminController::class, 'ajax_admin_resume_platform']);

     //project routes
     Route::get('project', [AdminController::class, 'add_project'])->name('project');
     Route::post('add-project', [AdminController::class, 'store_project']);
     Route::post('ajax/get-project', [AdminController::class, 'ajax_get_project']);
     Route::post('ajax/admin/get-project-for-edit', [AdminController::class, 'ajax_admin_get_project']);
     Route::post('update-project', [AdminController::class, 'update_project']);
     Route::post('ajax/admin/suspend-project', [AdminController::class, 'ajax_admin_suspend_project']);
     Route::post('ajax/admin/resume-project', [AdminController::class, 'ajax_admin_resume_project']);
});

require __DIR__.'/auth.php';
