<?php

use App\Http\Controllers\Admin\ApplicationReviewController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractTemplateController;
use App\Http\Controllers\Admin\MsfaaController;
use App\Http\Controllers\Admin\NoaController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AgentManagementController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\ApplicationStatusController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\Agent\AgentApplicationController;
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

    // Announcement routes (all authenticated users)
    Route::prefix('announcements')->middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\AnnouncementViewController::class, 'index'])->name('announcements.index');
        Route::get('/{announcement}', [App\Http\Controllers\AnnouncementViewController::class, 'show'])->name('announcements.show');
    });

    // Notification Settings Routes
    Route::prefix('settings')->middleware('auth')->group(function () {
        Route::get('/notifications', [App\Http\Controllers\NotificationSettingController::class, 'edit'])->name('settings.notifications');
        Route::put('/notifications', [App\Http\Controllers\NotificationSettingController::class, 'update'])->name('settings.notifications.update');
    });

    // Admin-only routes - User Management
    Route::middleware(['admin', 'super.admin'])->name('user-management.')->group(function () {
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
        Route::post('applications/{application}/update-intake', [ApplicationReviewController::class, 'updateIntake'])->name('applications.update-intake');
        Route::post('applications/{application}/update-program', [ApplicationReviewController::class, 'updateProgram'])->name('applications.update-program');
        Route::post('applications/{application}/update-agency', [ApplicationReviewController::class, 'updateAgency'])->name('applications.update-agency');
        Route::post('applications/{application}/update-funding', [ApplicationReviewController::class, 'updateFundingType'])->name('applications.update-funding');
        Route::post('applications/{application}/update-education', [ApplicationReviewController::class, 'updateEducation'])->name('applications.update-education');
        Route::post('applications/{application}/update-work', [ApplicationReviewController::class, 'updateWork'])->name('applications.update-work');
        Route::post('applications/{application}/update-documents', [ApplicationReviewController::class, 'updateDocuments'])->name('applications.update-documents');
        Route::get('applications/{application}/export-pdf', [ApplicationReviewController::class, 'exportPdf'])->name('applications.export-pdf');
        Route::get('applications/{application}/document/{documentType}', [ApplicationReviewController::class, 'downloadDocument'])->name('applications.download');
        
        // Contract Templates
        Route::resource('contract-templates', ContractTemplateController::class);

        // Contract Management
        Route::get('applications/{application}/contract/create', [ContractController::class, 'create'])->name('applications.contract.create');
        Route::post('applications/{application}/contract', [ContractController::class, 'store'])->name('applications.contract.store');
        Route::post('applications/{application}/contract/preview', [ContractController::class, 'preview'])->name('applications.contract.preview');
        Route::get('contract-templates/{template}/placeholders', [ContractController::class, 'getTemplatePlaceholders'])->name('contract-templates.placeholders');
        Route::get('contracts/{contract}/download-generated', [ContractController::class, 'downloadGenerated'])->name('contracts.download-generated');
        Route::get('contracts/{contract}/download-signed', [ContractController::class, 'downloadSigned'])->name('contracts.download-signed');
        Route::post('applications/{application}/contract/approve', [ContractController::class, 'approveContract'])->name('applications.contract.approve');
        Route::post('applications/{application}/contract/reject', [ContractController::class, 'rejectContract'])->name('applications.contract.reject');

        // Payment Management
        Route::get('applications/{application}/payment/download', [PaymentController::class, 'downloadReceipt'])->name('applications.payment.download');
        Route::post('applications/{application}/payment/approve', [PaymentController::class, 'approve'])->name('applications.payment.approve');
        Route::post('applications/{application}/payment/reject', [PaymentController::class, 'reject'])->name('applications.payment.reject');

        // NOA (Notice of Assessment) Management
        Route::post('applications/{application}/noa/request', [NoaController::class, 'request'])->name('applications.noa.request');
        Route::get('applications/{application}/noa/download', [NoaController::class, 'download'])->name('applications.noa.download');
        Route::post('applications/{application}/noa/approve', [NoaController::class, 'approve'])->name('applications.noa.approve');
        Route::post('applications/{application}/noa/reject', [NoaController::class, 'reject'])->name('applications.noa.reject');

        // MSFAA (Master Student Financial Assistance Agreement) Management
        Route::post('applications/{application}/msfaa/request', [MsfaaController::class, 'request'])->name('applications.msfaa.request');
        Route::post('applications/{application}/msfaa/approve', [MsfaaController::class, 'approve'])->name('applications.msfaa.approve');
        Route::post('applications/{application}/msfaa/reject', [MsfaaController::class, 'reject'])->name('applications.msfaa.reject');

        // System Announcements
        Route::resource('announcements', App\Http\Controllers\Admin\SystemAnnouncementController::class);
    });

    // Admin-only routes - Student Management (SIS is master for students)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
        Route::resource('students', StudentController::class);
        Route::post('students/{student}/suspend', [StudentController::class, 'suspend'])->name('students.suspend');
        Route::post('students/{student}/unsuspend', [StudentController::class, 'unsuspend'])->name('students.unsuspend');
    });

    // Admin-only routes - Agent Management
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('agents', AgentManagementController::class);
    });

    // Programs and Intakes are managed from LMS (master system)
    // SIS fetches programs/intakes via LMS API for application form

    // Agent portal routes
    Route::middleware(['agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('applications', [AgentApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/export', [AgentApplicationController::class, 'export'])->name('applications.export');
        Route::get('applications/create', [AgentApplicationController::class, 'createApplication'])->name('applications.create');
        Route::get('applications/confirmation/{reference}', [AgentApplicationController::class, 'confirmation'])->name('applications.confirmation');
        Route::get('applications/{application}', [AgentApplicationController::class, 'show'])->name('applications.show');

        // Agent application form steps (mirrors public /apply/* routes)
        Route::name('application.')->prefix('application')
            ->controller(StudentApplicationController::class)
            ->group(function () {
                Route::get('/', 'showStepOne')->name('step1');
                Route::post('/step-1', 'storeStepOne')->name('step1.store');
                Route::get('/step-2', 'showStepTwo')->name('step2');
                Route::post('/step-2', 'storeStepTwo')->name('step2.store');
                Route::get('/step-3', 'showStepThree')->name('step3');
                Route::post('/step-3', 'storeStepThree')->name('step3.store');
                Route::get('/step-4', 'showStepFour')->name('step4');
                Route::post('/step-4', 'storeStepFour')->name('step4.store');
                Route::get('/step-5', 'showStepFive')->name('step5');
                Route::post('/submit', 'submit')->name('submit');
            });
    });

    // Student portal routes
    Route::middleware(['student'])->prefix('student')->name('student.')->group(function () {
        // My Application - shows application status
        Route::get('application', [\App\Http\Controllers\Student\ProgramController::class, 'index'])->name('program.index');

        // My Courses - redirects to LMS with SSO
        Route::get('my-courses', [\App\Http\Controllers\Student\MyCoursesController::class, 'index'])->name('my-courses');
        Route::get('my-courses/redirect', [\App\Http\Controllers\Student\MyCoursesController::class, 'redirectToCourses'])->name('my-courses.redirect');

        // My Grades - redirects to LMS grades with SSO
        Route::get('my-grades/redirect', [\App\Http\Controllers\Student\MyCoursesController::class, 'redirectToGrades'])->name('my-grades.redirect');

        // Contract
        Route::get('contract/download', [\App\Http\Controllers\Student\StudentContractController::class, 'downloadContract'])->name('contract.download');
        Route::post('contract/upload-signed', [\App\Http\Controllers\Student\StudentContractController::class, 'uploadSignedContract'])->name('contract.upload-signed');

        // Payment
        Route::post('payment/upload-receipt', [\App\Http\Controllers\Student\StudentPaymentController::class, 'uploadReceipt'])->name('payment.upload-receipt');

        // NOA (Notice of Assessment)
        Route::post('noa/upload', [\App\Http\Controllers\Student\StudentNoaController::class, 'upload'])->name('noa.upload');
        Route::post('noa/skip', [\App\Http\Controllers\Student\StudentNoaController::class, 'skip'])->name('noa.skip');

        // MSFAA (Master Student Financial Assistance Agreement)
        Route::post('msfaa/confirm', [\App\Http\Controllers\Student\StudentMsfaaController::class, 'confirm'])->name('msfaa.confirm');

        // My Profile
        Route::get('profile', [\App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

require __DIR__.'/auth.php';
