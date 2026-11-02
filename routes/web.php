<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DownloadRequestController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Services\NotificationsService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-nots', function () {
    
});

Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');


Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);


Route::post('reset-password', [AuthController::class, 'storePassword'])
    ->name('password.store');

Route::get('/verify/{token}', [AccountManagerController::class, 'verify'])->name('account.verify');


Route::middleware(['auth:web'])->prefix('account')->group(function () {
    Route::get('/manage', [AccountManagerController::class, 'manageAccount'])->name('account.manage');
    Route::get('/manage/profiles', [AccountManagerController::class, 'profiles'])->name('account.profiles');
    Route::get('/manage/payment-method', [AccountManagerController::class, 'manageAccount'])->name('account.payment-method');
    Route::get('manage/subscriptions/{subscriptionId}/terms-of-service', [SubscriptionController::class, 'terms'])->name('subscription.terms');

    Route::get('/manage/subscriptions', [SubscriptionController::class, 'index'])->name('account.subscriptions');
    Route::post('subscription/{subscriptionId}/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('subscription/{subscriptionId}/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/download/{hash}', [DownloadRequestController::class, 'download'])->name('download');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware(['auth:web'])->name('logout');

Route::get('terms-of-service', function () {
    return view('web.terms-of-service');
})->name('terms-of-service');

Route::get('/request-delete-account', [AccountManagerController::class, 'requestDelete'])->name('account.request-delete');

Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

Route::get('/test-notification-logs', function() {
    \App\Jobs\NotifyAppUsers::dispatch(
        'NEW_AUDITION',
        \App\Models\Audition::latest()->first(),
        123,
        'audition_country'
    )->onQueue('notification-chunks');
});
