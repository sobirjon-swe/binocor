<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ConstructionStageController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::resource('projects', ProjectController::class);
    Route::resource('properties', PropertyController::class);
    Route::post('/properties/{property}/photos', [PropertyController::class, 'storePhoto'])->name('properties.photos.store');
    Route::delete('/properties/{property}/photos/{photo}', [PropertyController::class, 'destroyPhoto'])->name('properties.photos.destroy');
    Route::resource('customers', CustomerController::class);
    Route::resource('contracts', ContractController::class);
    Route::get('/contracts/{contract}/pdf', [ContractController::class, 'pdf'])->name('contracts.pdf');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::middleware(['auth', 'verified', 'role:admin|manager|accountant'])->group(function () {
    Route::resource('payments', PaymentController::class)->except('show');
});

Route::middleware(['auth', 'verified', 'role:admin|manager|foreman|chief_engineer'])->group(function () {
    Route::resource('construction-stages', ConstructionStageController::class);
    Route::post('/construction-stages/{constructionStage}/photos', [ConstructionStageController::class, 'storePhoto'])->name('construction-stages.photos.store');
    Route::delete('/construction-stages/{constructionStage}/photos/{photo}', [ConstructionStageController::class, 'destroyPhoto'])->name('construction-stages.photos.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin|manager|sales_manager'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)->except('show');
});

Route::middleware(['auth', 'verified', 'role:admin|manager'])->group(function () {
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Mijoz brauzerida ochiladigan onlayn to'lov havolalari — Binocor tizimiga
// kirmagan mijoz uchun, shuning uchun auth talab qilinmaydi. Xavfsizlik
// imzolangan URL (signed route) orqali ta'minlanadi.
Route::get('/payments/{payment}/pay/{provider}', [PaymentGatewayController::class, 'redirect'])->name('payments.pay');
Route::get('/payments/{payment}/pay-return', [PaymentGatewayController::class, 'returnPage'])->name('payments.pay.return');

// To'lov tizimlari (Payme, Click) tomonidan chaqiriladigan webhook'lar.
Route::post('/webhooks/payme', [PaymentGatewayController::class, 'payme'])->name('webhooks.payme');
Route::post('/webhooks/click', [PaymentGatewayController::class, 'click'])->name('webhooks.click');

require __DIR__.'/auth.php';
