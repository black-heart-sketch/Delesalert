<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BillController;
use App\Http\Controllers\Web\CommunityController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\MapSettingsController;
use App\Http\Controllers\Web\NotificationCenterController;
use App\Http\Controllers\Web\OutageController;
use App\Http\Controllers\Web\OutageManagementController;
use App\Http\Controllers\Web\ReportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/outages', [OutageController::class, 'index'])->name('outages.index');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::post('/demo-login', [AuthController::class, 'demoLogin'])->middleware('throttle:login')->name('demo-login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'createAccount'])->name('register.store');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::post('/community/{post}/comments', [CommunityController::class, 'comment'])->name('community.comments.store');
    Route::post('/community/{post}/reactions', [CommunityController::class, 'react'])->name('community.reactions.store');
    Route::middleware('role:CLIENT')->group(function () {
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/read-all', [NotificationCenterController::class, 'readAll'])->name('notifications.read-all');
        Route::patch('/notifications/{notification}/read', [NotificationCenterController::class, 'read'])->name('notifications.read');
        Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
        Route::post('/bills/{bill}/pay', [BillController::class, 'pay'])->name('bills.pay');
    });
    Route::middleware('role:PROVIDER,ADMIN')->group(function () {
        Route::get('/manage/outages', [OutageManagementController::class, 'index'])->name('outages.manage');
        Route::post('/manage/outages', [OutageManagementController::class, 'store'])->name('outages.manage.store');
        Route::patch('/manage/outages/{outage}', [OutageManagementController::class, 'update'])->name('outages.manage.update');
    });
    Route::middleware('role:ADMIN')->group(function () {
        Route::patch('/community/{post}/hide', [CommunityController::class, 'hide'])->name('community.hide');
        Route::get('/admin/map-settings', [MapSettingsController::class, 'edit'])->name('admin.map-settings.edit');
        Route::put('/admin/map-settings', [MapSettingsController::class, 'update'])->name('admin.map-settings.update');
    });
});
