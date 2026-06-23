<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentPortalController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $courses = Course::query()
        ->where('status', Course::STATUS_ATIVO)
        ->where('active', true)
        ->orderBy('start_date')
        ->orderBy('name')
        ->get();

    return view('welcome', [
        'courses' => $courses,
    ]);
})->name('welcome');

// Student Guest Routes
Route::middleware('guest:student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('login.perform');
    Route::get('/register', [StudentAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [StudentAuthController::class, 'register'])->name('register.perform');

    Route::get('/forgot-password', [StudentAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [StudentAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [StudentAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [StudentAuthController::class, 'resetPassword'])->name('password.update');
});

// Admin Guest Routes
Route::middleware('guest:web')->prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home');
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.perform');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AuthController::class, 'register'])->name('admin.register.perform');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');
});

// Shared Logout Route
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    if (Auth::guard('student')->check()) {
        Auth::guard('student')->logout();
    } else {
        Auth::guard('web')->logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('welcome');
})->name('logout');

// Public Validation Route
Route::get('/validar/{code}', [CertificateController::class, 'validateCertificate'])->name('certificates.validate');

// Student Authenticated Routes
Route::middleware('auth:student')->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'index'])->name('student.dashboard');
    Route::get('/courses/{course}/enroll', [StudentPortalController::class, 'enroll'])->name('student.courses.enroll');
    Route::get('/certificates/{certificate}/pdf', [StudentPortalController::class, 'downloadCertificate'])->name('student.certificates.pdf');
    Route::get('/profile', [StudentPortalController::class, 'editProfile'])->name('student.profile.edit');
    Route::put('/profile', [StudentPortalController::class, 'updateProfile'])->name('student.profile.update');
});

// Admin Authenticated Routes
Route::middleware('auth:web')->prefix('admin')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/show', [CertificateController::class, 'show'])->name('certificates.show');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'pdf'])->name('certificates.pdf');
    Route::post('/certificates/{certificate}/reissue', [CertificateController::class, 'reissue'])->name('certificates.reissue');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::post('/courses/{course}/close', [CourseController::class, 'close'])->name('courses.close');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/students', [CourseController::class, 'storeStudent'])->name('courses.students.store');
    Route::get('/courses/{course}/students/{student}', [CourseController::class, 'showStudent'])->name('courses.students.show');
    Route::get('/courses/{course}/students/{student}/edit', [CourseController::class, 'editStudent'])->name('courses.students.edit');
    Route::put('/courses/{course}/students/{student}', [CourseController::class, 'updateStudent'])->name('courses.students.update');

    Route::get('/signature', [SignatureController::class, 'index'])->name('signature.index');
    Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
});

