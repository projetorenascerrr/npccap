<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CertificateController::class, 'index'])->name('certificates.index');
Route::get('/certificates/show', [CertificateController::class, 'show'])->name('certificates.show');
Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'pdf'])->name('certificates.pdf');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/students', [CourseController::class, 'storeStudent'])->name('courses.students.store');
Route::get('/courses/{course}/students/{student}/edit', [CourseController::class, 'editStudent'])->name('courses.students.edit');
Route::put('/courses/{course}/students/{student}', [CourseController::class, 'updateStudent'])->name('courses.students.update');

Route::get('/signature', [SignatureController::class, 'index'])->name('signature.index');
Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');
