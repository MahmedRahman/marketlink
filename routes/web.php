<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialRecordController;
use App\Http\Controllers\TaskController;

// الصفحة الرئيسية: Landing Page فقط
Route::get('/', fn () => view('landing'))->name('home');

// تسجيل الدخول على مسار منفصل
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard (يحتاج تسجيل دخول)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// العملاء - CRUD (يحتاج تسجيل دخول)
Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('/api/customers', [CustomerController::class, 'apiIndex'])->name('customers.api.index');
    
    // المشاريع - CRUD
    Route::resource('projects', ProjectController::class);
    
    // الموظفين - CRUD
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/update-order', [EmployeeController::class, 'updateOrder'])->name('employees.update-order');
    
    // الاشتراكات - CRUD
    Route::resource('subscriptions', SubscriptionController::class);
    
    // تقرير الحسابات
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/payment-status', [ReportController::class, 'paymentStatus'])->name('reports.payment-status');
    Route::post('/reports/create-records', [ReportController::class, 'createRecords'])->name('reports.create-records');
    
    // السجلات المالية
    Route::get('/financial-records/{financialRecord}', [FinancialRecordController::class, 'show'])->name('financial-records.show');
    Route::post('/financial-records', [FinancialRecordController::class, 'store'])->name('financial-records.store');
    Route::put('/financial-records/{financialRecord}', [FinancialRecordController::class, 'update'])->name('financial-records.update');
    Route::patch('/financial-records/{financialRecord}/payment-status', [FinancialRecordController::class, 'updatePaymentStatus'])->name('financial-records.payment-status');
    Route::delete('/financial-records/{financialRecord}', [FinancialRecordController::class, 'destroy'])->name('financial-records.destroy');
    
    // المهام
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/update-order', [TaskController::class, 'updateOrder'])->name('tasks.update-order');
});

// صفحة الموظف (بدون تسجيل دخول)
Route::get('/tasks/employee/{phone}', [TaskController::class, 'employeeView'])->where('phone', '.*')->name('tasks.employee');
Route::post('/tasks/employee/{phone}', [TaskController::class, 'employeeStore'])->where('phone', '.*')->name('tasks.employee.store');
Route::put('/tasks/employee/{phone}/{task}', [TaskController::class, 'employeeUpdate'])->where('phone', '.*')->name('tasks.employee.update');
Route::delete('/tasks/employee/{phone}/{task}', [TaskController::class, 'employeeDestroy'])->where('phone', '.*')->name('tasks.employee.destroy');
