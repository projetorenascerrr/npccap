<?php

use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CertificateController::class, 'index'])->name('certificates.index');
Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'pdf'])->name('certificates.pdf');
