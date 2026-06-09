<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\StudentController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $courses = Course::query()
        ->where('status', Course::STATUS_ATIVO)
        ->orderBy('start_date')
        ->orderBy('name')
        ->get();

    return view('welcome', [
        'courses' => $courses,
    ]);
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/validar/{code}', [CertificateController::class, 'validateCertificate'])->name('certificates.validate');

Route::middleware('auth')->group(function () {
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
