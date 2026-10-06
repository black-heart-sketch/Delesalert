<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OutageController;
use App\Http\Controllers\Api\PredictionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
Route::get('outages/current', [OutageController::class, 'current']);
Route::get('outages/scheduled', [OutageController::class, 'scheduled']);
Route::get('map/configuration', [AdminController::class, 'mapConfiguration']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::middleware('role:CLIENT')->group(function () {
        Route::get('profile', fn (Request $request) => ['success' => true, 'data' => $request->user()]);
        Route::patch('profile', [ProfileController::class, 'update']);
        Route::patch('profile/password', [ProfileController::class, 'updatePassword']);
        Route::delete('profile', [ProfileController::class, 'destroy']);
        Route::apiResource('locations', LocationController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'read']);
        Route::get('notification-preferences', [NotificationController::class, 'preference']);
        Route::patch('notification-preferences', [NotificationController::class, 'updatePreference']);
        Route::post('reports', [ReportController::class, 'store']);
        Route::get('reports/my', [ReportController::class, 'mine']);
        Route::get('bills', [BillController::class, 'index']);
        Route::post('bills/{bill}/payments', [BillController::class, 'store'])->middleware('throttle:payments');
        Route::post('bill-payments/{payment}/refresh', [BillController::class, 'refresh'])->middleware('throttle:payments');
    });
    Route::middleware('role:CLIENT,ADMIN')->group(function () {
        Route::get('outages/history', [OutageController::class, 'history']);
        Route::get('predictions', [PredictionController::class, 'index']);
        Route::get('predictions/{zone}', [PredictionController::class, 'forZone']);
    });
    Route::middleware('role:PROVIDER,ADMIN')->group(function () {
        Route::post('outages', [OutageController::class, 'store']);
        Route::patch('outages/{outage}', [OutageController::class, 'update']);
        Route::apiResource('provider/incidents', IncidentController::class)->except('destroy');
    });
    Route::middleware('role:ADMIN')->prefix('admin')->group(function () {
        Route::get('users', [AdminController::class, 'users']);
        Route::patch('users/{user}/status', [AdminController::class, 'userStatus']);
        Route::get('zones', [AdminController::class, 'zones']);
        Route::post('zones', [AdminController::class, 'storeZone']);
        Route::patch('zones/{zone}', [AdminController::class, 'updateZone']);
        Route::delete('zones/{zone}', [AdminController::class, 'deleteZone']);
        Route::get('reports', [AdminController::class, 'reports']);
        Route::patch('reports/{report}/status', [AdminController::class, 'reviewReport']);
        Route::put('map-configuration', [AdminController::class, 'updateMapConfiguration']);
        Route::post('predictions/generate', [PredictionController::class, 'generate']);
    });
});
Route::get('outages/{outage}', [OutageController::class, 'show']);
