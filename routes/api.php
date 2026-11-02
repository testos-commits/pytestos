<?php

use App\Http\Controllers\ApplicationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Middleware\SetAppLanguage;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PresignedUrlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('general/contact', [GeneralController::class, 'sendContact']);

Route::middleware(SetAppLanguage::class)->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'sendResetPasswordLink']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('general')->group(function () {
        Route::get('/', [GeneralController::class, 'getGeneralData'])->name('general-data');
        Route::get('/faqs', [GeneralController::class, 'getFaqs'])->name('faqs');
        Route::get('/app-state', [GeneralController::class, 'getAppState'])->name('app-state');
    });


    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/auth/request-manage-account', [AccountManagerController::class, 'authorizeRequest'])->name('request-manage-account');
        /** AUDITIONS */
        Route::prefix('auditions')->group(function () {
            Route::get('/', [AuditionsController::class, 'index'])->name('auditions.index');
            Route::get('/for-you', [AuditionsController::class, 'forYou'])->name('auditions.for-you');
            Route::get('/{id}', [AuditionsController::class, 'show'])->whereNumber('id')->name('auditions.show');
            Route::post('/{auditionId}/apply', [ApplicationsController::class, 'apply'])->whereNumber('id')->name('auditions.apply');
            Route::get('/user/profile/applications', [ApplicationsController::class, 'getProfileApplications'])->name('profile.applications');
            Route::get('/user/profile/shortlists', [ApplicationsController::class, 'getProfileShortlists'])->name('profile.shotlists');
            Route::get('/user/profile/accepted', [ApplicationsController::class, 'getProfileAccepted'])->name('profile.accepted');
        });

        Route::prefix('profile/notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');

            Route::get('/has-unread', [NotificationController::class, 'hasUnread'])->name('notifications.has-unread');

            Route::get('/mark-read', [NotificationController::class, 'updateLastSeen'])->name('notifications.mark-read');

            Route::prefix('v2')->group(function () {
                Route::get('/has-unread', [NotificationController::class, 'hasUnreadV2'])->name('notifications.has-unread-v2');
            });
        });

        /** PROFILE */
        Route::prefix('/user')->group(function () {
            Route::post('/payment-details', [ProfileController::class, 'createPaymentDetails'])->name('user.createPaymentDetails');
            Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
            Route::post('/language/switch/{language}', [ProfileController::class, 'switchLanguage'])->name('profile.switchLanguage');

            Route::prefix('profile')->group(function () {
                Route::post('/', [ProfileController::class, 'create'])->name('profile.create');
                Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
                Route::post('/media', [ProfileController::class, 'saveMediaAssetURL'])->name('profile.saveMediaAssetURL');
                Route::delete('/image/{id}', [ProfileController::class, 'deleteProfileImage'])->whereNumber('id')->name('profile.deleteProfileImage');
                Route::post('/{profile}/switch', [ProfileController::class, 'switchProfile'])->name('profile.switch');
                Route::post('delete', [ProfileController::class, 'deleteProfile'])->name('profile.delete');
                Route::post('/previous-commercials', [ProfileController::class, 'addPreviousCommercial'])->name('profile.addPreviousCommercial');
            });

            Route::prefix('uploads')->group(function () {
                Route::post('/presign-upload-url', [PresignedUrlController::class, 'generatePresignedUrl'])->name('upload.presigned-url');
                Route::post('/presign-upload-urls', [PresignedUrlController::class, 'generatePresignedUrls'])->name('upload.presigned-urls');
            });


            Route::post('/account/delete', [ProfileController::class, 'deleteAccount'])->name('account.delete');
        });


        /** MESSAGES */
        Route::prefix('messages')->group(function () {
            Route::get('/', [MessagesController::class, 'index'])->name('messages.applied-auditions');
            Route::get('/{auditionId}', [MessagesController::class, 'show'])->name('messages.show');
            Route::post('/{auditionMessage}/answer', [MessagesController::class, 'answer'])->name('messages.answer');
            Route::post('/{auditionId}/mark-read', [MessagesController::class, 'markRead'])->name('messages.mark-read');
            Route::post('/admin/update-last-read', [MessagesController::class, 'updateLastReadAdminMessage'])->name('admin-messages.update-last-read');
        });

        /** NEWS */
        Route::prefix('news')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('news.index');
            Route::get('/{newsId}', [NewsController::class, 'show'])->whereNumber('newsId')->name('news.show');
            Route::post('/{newsId}/like', [NewsController::class, 'changeLikeStatus'])->whereNumber('newsId')->name('news.like');
        });


        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
