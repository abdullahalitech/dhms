<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //Admin Routes starts
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('adminDashboard');
    Route::get('monthly-earning-details', [AdminController::class, 'monthly_earning'])->name('adminDashboard');
    Route::get('alltime-earning-details', [AdminController::class, 'alltime_earning'])->name('adminDashboard');
    
    //employee routes
    Route::get('employee', [AdminController::class, 'add_employee'])->name('employee');
    Route::get('employee/report/{id}', [AdminController::class, 'employee_report'])->name('employee');
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
     Route::get('projects/{id}', [AdminController::class, 'add_project'])->name('project');
     Route::get('project/{id}', [AdminController::class, 'project_single'])->name('project');
     Route::post('add-project', [AdminController::class, 'store_project']);
     Route::post('edit-project', [AdminController::class, 'edit_project']);
     Route::post('ajax/get-project', [AdminController::class, 'ajax_get_project']);
     Route::post('ajax/admin/get-project-for-edit', [AdminController::class, 'ajax_admin_get_project']);
     Route::post('update-project', [AdminController::class, 'update_project']);
     Route::post('ajax/admin/suspend-project', [AdminController::class, 'ajax_admin_suspend_project']);
     Route::post('ajax/admin/resume-project', [AdminController::class, 'ajax_admin_resume_project']);
     Route::post('set-deadline', [AdminController::class, 'set_deadline']);
     Route::post('set-assign', [AdminController::class, 'set_assign']);
     Route::post('set-share', [AdminController::class, 'set_share']);
     Route::post('ajax/admin/mark-completed', [AdminController::class, 'ajax_admin_mark_completed']);

    //bid invite routes
    Route::get('bid', [AdminController::class, 'add_bid'])->name('bid');
    Route::get('invite', [AdminController::class, 'add_invite'])->name('invite');
    Route::post('add-bidinvite', [AdminController::class, 'store_bidinvite']);
    Route::post('ajax/get-bid-invite', [AdminController::class, 'ajax_get_bidinvite']);
    Route::post('ajax/admin/get-bidinvite-for-edit', [AdminController::class, 'ajax_admin_get_bidinvite']);
    Route::post('update-bidinvite', [AdminController::class, 'update_bidinvite']);
    Route::post('ajax/admin/suspend-bidinvite', [AdminController::class, 'ajax_admin_suspend_bidinvite']);
    Route::post('ajax/admin/resume-bidinvite', [AdminController::class, 'ajax_admin_resume_bidinvite']);

     
     //Route::get('insert-commission', [AdminController::class, 'insert_commission'])->name('invite');
    
    

    //Admin Routes ends


    //normal employee roues start
    
    Route::get('user/dashboard', [EmployeeController::class, 'index'])->name('employeeDashboard');

    //project routes
    Route::get('user/projects', [EmployeeController::class, 'user_add_project'])->name('userProject');
    Route::get('user/project/{id}', [EmployeeController::class, 'user_project_single'])->name('userProject');
    Route::post('user/add-project', [EmployeeController::class, 'user_store_project']);
    Route::post('user/edit-project', [EmployeeController::class, 'user_edit_project']);
    Route::post('user/ajax/get-project', [EmployeeController::class, 'user_ajax_get_project']);
    Route::post('ajax/user/get-project-for-edit', [EmployeeController::class, 'ajax_user_get_project']);
    Route::post('user/update-project', [EmployeeController::class, 'user_update_project']);
    Route::post('ajax/user/suspend-project', [EmployeeController::class, 'ajax_user_suspend_project']);
    Route::post('ajax/user/resume-project', [EmployeeController::class, 'ajax_user_resume_project']);
    Route::post('user/set-deadline', [EmployeeController::class, 'user_set_deadline']);
    Route::post('user/set-assign', [EmployeeController::class, 'user_set_assign']);
    Route::post('user/set-share', [EmployeeController::class, 'user_set_share']);
    Route::post('ajax/user/mark-completed', [EmployeeController::class, 'ajax_user_mark_completed']);

    
    Route::get('user/projects/shared', [EmployeeController::class, 'user_add_project_shared'])->name('userSharedProject');
    Route::get('user/project/shared/{id}', [EmployeeController::class, 'user_project_single_shared'])->name('userSharedProject');
    Route::post('user/ajax/get-project-shared', [EmployeeController::class, 'user_ajax_get_project_shared']);
   //Admin Routes ends


    //normal employee roues ends

});

require __DIR__.'/auth.php';
