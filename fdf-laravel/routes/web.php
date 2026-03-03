<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAnalyticsExportController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ContactController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\CourseManager;
use App\Livewire\Admin\LessonManager;
use App\Livewire\Admin\EnrollmentManager;
use App\Livewire\Admin\EventManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\LmsDashboard;
use App\Livewire\Admin\RolePermissionManager;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\HeroSlideManager;
use App\Livewire\Admin\EmailTemplateManager;
use App\Livewire\Admin\SiteSettingsManager;

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        
        // Content Management Routes
        Route::get('/pages', \App\Livewire\Admin\PageManager::class)->name('pages');
        Route::get('/events', EventManager::class)->name('events');
        Route::get('/blog', \App\Livewire\Admin\BlogManager::class)->name('blog');
        Route::get('/categories', CategoryManager::class)->name('categories');
        Route::get('/hero-slides', HeroSlideManager::class)->name('hero-slides');
        Route::get('/email-templates', EmailTemplateManager::class)->name('email-templates');
        Route::get('/site-settings', SiteSettingsManager::class)->name('site-settings');
        
        // LMS Management Routes
        Route::get('/lms', LmsDashboard::class)->name('lms');
        Route::get('/courses', CourseManager::class)->name('courses');
        Route::get('/lessons', LessonManager::class)->name('lessons');
        Route::get('/enrollments', EnrollmentManager::class)->name('enrollments');
        
        // User Management Routes
        Route::get('/users', UserManager::class)->name('users');
        Route::get('/roles-permissions', RolePermissionManager::class)->name('roles');
        Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');
        Route::get('/analytics/export/pdf', [AdminAnalyticsExportController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::view('/manual', 'admin.manual')->name('manual');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });
});

// Public Authentication Routes
Route::middleware('guest')->group(function () {
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
    Route::post('/dashboard/payments/{enrollment}', [UserDashboardController::class, 'processPayment'])->name('dashboard.pay.submit');
});

// Homepage
Route::get('/', [PageController::class, 'home'])->name('home');

// Events Routes - Must come before dynamic pages
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
    Route::get('/{slug}/register', [EventController::class, 'register'])->name('register');
    Route::post('/{slug}/register', [EventController::class, 'submitRegistration'])->name('register.submit');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

// Blog Routes - Must come before dynamic pages
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{blogPost:slug}', [BlogController::class, 'show'])->name('show');
});

// Courses & Enrollment Routes - Must come before dynamic pages
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{course:slug}', [CourseController::class, 'show'])->name('show');
    Route::get('/{course:slug}/lessons/{lesson:slug}', [CourseController::class, 'showLesson'])->name('lessons.show');
    Route::post('/{course:slug}/lessons/{lesson:slug}/complete', [CourseController::class, 'completeLesson'])
        ->middleware('auth')
        ->name('lessons.complete');
    Route::post('/{course:slug}/enroll', [CourseController::class, 'enroll'])->name('enroll');
});

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/programs-and-activities', [PageController::class, 'programs'])->name('programs');
Route::get('/donations', [PageController::class, 'donations'])->name('donations');
Route::get('/accessibility', [PageController::class, 'accessibility'])->name('accessibility');

// Dynamic Pages (for any additional pages) - Must be last
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
