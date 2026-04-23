<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PasswordChangeController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/change-password', [PasswordChangeController::class, 'showForm'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'change']);

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/register', [UserRegisterController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [UserRegisterController::class, 'register']);
    });

    // Document routes
    Route::get('/upload', [DocumentController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
    Route::get('/download/{id}', [DocumentController::class, 'download'])->name('download');
    Route::get('/search', [DocumentController::class, 'search'])->name('search');
});
