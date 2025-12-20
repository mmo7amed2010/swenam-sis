<?php

use App\Http\Controllers\Admin\ApplicationReviewController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\ApplicationStatusController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\TranslationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Student Information System (SIS)
|--------------------------------------------------------------------------
|
| This file contains routes for the Student Information System.
| SIS handles: Applications, Application Review, Student Management
| Learning features (courses, assignments, quizzes) are in the LMS.
|
*/

// Public Student Application Routes (Guest-accessible)
Route::name('application.')->prefix('apply')->group(function () {
    // Step 1 - Program Information
    Route::get('/', [StudentApplicationController::class, 'showStepOne'])->name('step1');
    Route::post('/step-1', [StudentApplicationController::class, 'storeStepOne'])->name('step1.store');

    // Step 2 - Personal Information
    Route::get('/step-2', [StudentApplicationController::class, 'showStepTwo'])->name('step2');
    Route::post('/step-2', [StudentApplicationController::class, 'storeStepTwo'])->name('step2.store');

    // Step 3 - Education History
    Route::get('/step-3', [StudentApplicationController::class, 'showStepThree'])->name('step3');
    Route::post('/step-3', [StudentApplicationController::class, 'storeStepThree'])->name('step3.store');

    // Step 4 - Work History
    Route::get('/step-4', [StudentApplicationController::class, 'showStepFour'])->name('step4');
    Route::post('/step-4', [StudentApplicationController::class, 'storeStepFour'])->name('step4.store');

    // Step 5 - Supporting Documents
    Route::get('/step-5', [StudentApplicationController::class, 'showStepFive'])->name('step5');
    Route::post('/submit', [StudentApplicationController::class, 'submit'])->name('submit');

    // Confirmation
    Route::get('/confirmation/{reference}', [StudentApplicationController::class, 'confirmation'])->name('confirmation');
});

// Public Application Status Check Routes (Guest-accessible)
Route::name('application.status')->group(function () {
    Route::get('/application/status', [ApplicationStatusController::class, 'index']);
    Route::post('/application/status/check', [ApplicationStatusController::class, 'check'])
        ->name('.check');
});

// Public homepage (guest users)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware(['auth', 'password.reset.required'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('dashboard.access');

    // Notification routes (all authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [\App\Http\Controllers\NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/api/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Notification Settings Routes
    Route::prefix('settings')->middleware('auth')->group(function () {
        Route::get('/notifications', [App\Http\Controllers\NotificationSettingController::class, 'edit'])->name('settings.notifications');
        Route::put('/notifications', [App\Http\Controllers\NotificationSettingController::class, 'update'])->name('settings.notifications.update');
    });

    // Public Announcement Viewing Routes (for all authenticated users)
    Route::prefix('announcements')->middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\AnnouncementViewController::class, 'index'])->name('announcements.index');
        Route::get('/{announcement}', [App\Http\Controllers\AnnouncementViewController::class, 'show'])->name('announcements.show');
    });

    // Admin-only routes - User Management
    Route::middleware(['admin'])->name('user-management.')->group(function () {
        Route::resource('/user-management/users', UserManagementController::class);
    });

    // Admin-only routes - Translations
    Route::middleware(['admin'])->name('translations.')->group(function () {
        Route::get('/translations-management', [TranslationsController::class, 'index'])->name('index');
        Route::post('/translations-management/contributors/save', [TranslationsController::class, 'saveContributor'])->name('contributors.save');
        Route::get('/translations-management/contributors/{id}', [TranslationsController::class, 'getContributor'])->name('contributors.show');
        Route::delete('/translations-management/contributors/{id}', [TranslationsController::class, 'deleteContributor'])->name('contributors.delete');
    });

    // Admin-only routes - Application Review Management
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
        Route::get('applications/export', [ApplicationReviewController::class, 'export'])->name('applications.export');
        Route::get('applications/{application}', [ApplicationReviewController::class, 'show'])->name('applications.show');
        Route::post('applications/{application}/initial-approve', [ApplicationReviewController::class, 'initialApprove'])->name('applications.initial-approve');
        Route::post('applications/{application}/approve', [ApplicationReviewController::class, 'approve'])->name('applications.approve');
        Route::post('applications/{application}/reject', [ApplicationReviewController::class, 'reject'])->name('applications.reject');
        Route::get('applications/{application}/document/{documentType}', [ApplicationReviewController::class, 'downloadDocument'])->name('applications.download');
    });

    // Admin-only routes - Student Management (SIS is master for students)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('students', StudentController::class);
    });

    // Programs and Intakes are managed from LMS (master system)
    // SIS fetches programs/intakes via LMS API for application form

    // Admin-only routes - System Announcements
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('announcements', \App\Http\Controllers\Admin\SystemAnnouncementController::class);
    });

    // Student portal routes
    Route::middleware(['student'])->prefix('student')->name('student.')->group(function () {
        // My Application - shows application status or link to LMS courses
        Route::get('application', [\App\Http\Controllers\Student\ProgramController::class, 'index'])->name('program.index');

        // My Courses - redirects to LMS with SSO
        Route::get('my-courses', [\App\Http\Controllers\Student\MyCoursesController::class, 'index'])->name('my-courses');
        Route::get('my-courses/redirect', [\App\Http\Controllers\Student\MyCoursesController::class, 'redirectToLms'])->name('my-courses.redirect');
    });
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

require __DIR__.'/auth.php';
