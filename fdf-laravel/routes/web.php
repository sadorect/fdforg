<?php

use App\Http\Controllers\AdminAnalyticsExportController;
use App\Http\Controllers\AdminContentTransferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserDashboardController;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\BlogManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\ContentTransferManager;
use App\Livewire\Admin\CourseManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EmailTemplateManager;
use App\Livewire\Admin\EnrollmentManager;
use App\Livewire\Admin\EventManager;
use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\HeroSlideManager;
use App\Livewire\Admin\LessonManager;
use App\Livewire\Admin\LmsDashboard;
use App\Livewire\Admin\PageManager;
use App\Livewire\Admin\RolePermissionManager;
use App\Livewire\Admin\SiteSettingsManager;
use App\Livewire\Admin\UserManager;
use App\Models\Course;
use App\Models\Lesson;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/captcha', [AdminController::class, 'refreshCaptcha'])->name('captcha');
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // Content Management Routes
        Route::middleware('can:'.AdminPermissions::MANAGE_PAGES)->group(function () {
            Route::get('/pages', PageManager::class)->name('pages');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_EVENTS)->group(function () {
            Route::get('/events', EventManager::class)->name('events');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_GALLERY)->group(function () {
            Route::get('/gallery', GalleryManager::class)->name('gallery');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_BLOG)->group(function () {
            Route::get('/blog', BlogManager::class)->name('blog');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_CATEGORIES)->group(function () {
            Route::get('/categories', CategoryManager::class)->name('categories');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_HERO_SLIDES)->group(function () {
            Route::get('/hero-slides', HeroSlideManager::class)->name('hero-slides');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_EMAIL_TEMPLATES)->group(function () {
            Route::get('/email-templates', EmailTemplateManager::class)->name('email-templates');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_SITE_SETTINGS)->group(function () {
            Route::get('/site-settings', SiteSettingsManager::class)->name('site-settings');
        });
        Route::get('/content-transfer', ContentTransferManager::class)->name('content-transfer');
        Route::middleware('can:'.AdminPermissions::MANAGE_PAGES)->group(function () {
            Route::get('/content-transfer/pages/export', [AdminContentTransferController::class, 'exportPages'])->name('content-transfer.pages.export');
            Route::get('/content-transfer/pages/{page}/export', [AdminContentTransferController::class, 'exportPage'])->name('content-transfer.page.export');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_SITE_SETTINGS)->group(function () {
            Route::get('/content-transfer/site-settings/export', [AdminContentTransferController::class, 'exportSiteSettings'])->name('content-transfer.site-settings.export');
        });
        Route::get('/content-transfer/bundles/{bundle}/export', [AdminContentTransferController::class, 'exportBundle'])->name('content-transfer.bundle.export');

        // LMS Management Routes
        Route::middleware('can:'.AdminPermissions::VIEW_LMS_DASHBOARD)->group(function () {
            Route::get('/lms', LmsDashboard::class)->name('lms');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_COURSES)->group(function () {
            Route::get('/courses', CourseManager::class)->name('courses');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_LESSONS)->group(function () {
            Route::get('/lessons', LessonManager::class)->name('lessons');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_ENROLLMENTS)->group(function () {
            Route::get('/enrollments', EnrollmentManager::class)->name('enrollments');
        });

        // User Management Routes
        Route::middleware('can:'.AdminPermissions::MANAGE_USERS)->group(function () {
            Route::get('/users', UserManager::class)->name('users');
        });
        Route::middleware('can:'.AdminPermissions::MANAGE_ROLES_PERMISSIONS)->group(function () {
            Route::get('/roles-permissions', RolePermissionManager::class)->name('roles');
        });
        Route::middleware('can:'.AdminPermissions::VIEW_ANALYTICS)->group(function () {
            Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');
        });
        Route::middleware('can:'.AdminPermissions::EXPORT_ANALYTICS)->group(function () {
            Route::get('/analytics/export/pdf', [AdminAnalyticsExportController::class, 'exportPdf'])->name('analytics.export.pdf');
        });
        Route::view('/manual', 'admin.manual')->name('manual');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });
});

// Public Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/captcha', [AuthController::class, 'refreshCaptcha'])->name('auth.captcha');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/payments', [UserDashboardController::class, 'payments'])->name('dashboard.payments');
    Route::get('/dashboard/enrollments/{enrollment}/continue', [UserDashboardController::class, 'continueLearning'])
        ->name('dashboard.enrollments.continue');
    Route::get('/dashboard/payments/{enrollment}', [UserDashboardController::class, 'showPayment'])->name('dashboard.pay');
    Route::get('/dashboard/payments/{enrollment}/captcha', [UserDashboardController::class, 'refreshPaymentCaptcha'])->name('dashboard.pay.captcha');
    Route::post('/dashboard/payments/{enrollment}', [UserDashboardController::class, 'processPayment'])->name('dashboard.pay.submit');
});

// Homepage
Route::get('/', [PageController::class, 'home'])->name('home');

// Events Routes - Must come before dynamic pages
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
    Route::get('/{slug}/captcha', [EventController::class, 'refreshRegistrationCaptcha'])->name('captcha');
    Route::get('/{slug}/register', [EventController::class, 'register'])->name('register');
    Route::post('/{slug}/register', [EventController::class, 'submitRegistration'])->name('register.submit');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

// Blog Routes - Must come before dynamic pages
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{blogPost:slug}', [BlogController::class, 'show'])->name('show');
});

// Learning Routes - Must come before dynamic pages
Route::prefix('learning')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{course:slug}', [CourseController::class, 'show'])->name('show');
    Route::get('/{course:slug}/captcha', [CourseController::class, 'refreshEnrollmentCaptcha'])->middleware('auth')->name('captcha');
    Route::get('/{course:slug}/lessons/{lesson:slug}', [CourseController::class, 'showLesson'])->name('lessons.show');
    Route::get('/{course:slug}/lessons/{lesson:slug}/captcha', [CourseController::class, 'refreshLessonCaptcha'])
        ->middleware('auth')
        ->name('lessons.captcha');
    Route::post('/{course:slug}/lessons/{lesson:slug}/complete', [CourseController::class, 'completeLesson'])
        ->middleware('auth')
        ->name('lessons.complete');
    Route::post('/{course:slug}/enroll', [CourseController::class, 'enroll'])->name('enroll');
});

Route::prefix('courses')->group(function () {
    Route::get('/', function () {
        return redirect()->route('courses.index');
    });
    Route::get('/{course:slug}/lessons/{lesson:slug}', function (Course $course, Lesson $lesson) {
        return redirect()->route('courses.lessons.show', [$course, $lesson]);
    });
    Route::post('/{course:slug}/lessons/{lesson:slug}/complete', [CourseController::class, 'completeLesson'])
        ->middleware('auth');
    Route::post('/{course:slug}/enroll', [CourseController::class, 'enroll']);
    Route::get('/{course:slug}', function (Course $course) {
        return redirect()->route('courses.show', $course);
    });
});

// Static Pages
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/contact/captcha', [ContactController::class, 'refreshCaptcha'])->name('contact.captcha');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/programs-and-activities', [PageController::class, 'programs'])->name('programs');
Route::get('/donations', [PageController::class, 'donations'])->name('donations');
Route::get('/accessibility', [PageController::class, 'accessibility'])->name('accessibility');

// Dynamic Pages (for any additional pages) - Must be last
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
