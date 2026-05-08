<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DocumentApprovalController;

// ========== GUEST ROUTES (No login required) ==========

// Login routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Registration routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Home page (redirect to login if not authenticated)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ========== AUTHENTICATED ROUTES (Login required) ==========

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');

    // Document routes
    Route::get('/upload', [DocumentController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
    Route::get('/download/{id}', [DocumentController::class, 'download'])->name('download');
    Route::get('/preview/{id}', [DocumentController::class, 'preview'])->name('preview');
    Route::get('/search', [DocumentController::class, 'search'])->name('search');

    // Admin user management (only admin can access)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users');
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // Document routes
Route::middleware('auth')->group(function () {
    Route::get('/documents/edit/{id}', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/my-uploads', [DocumentController::class, 'myUploads'])->name('documents.my-uploads');
    Route::get('/documents/fetch', [DocumentController::class, 'fetchDocuments'])->name('documents.fetch');
Route::get('/images/{id}', [DocumentController::class, 'getImage'])->name('images.show');
    // Admin approval routes
    Route::middleware('admin-or-kmchampion')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/pending-documents', [DocumentApprovalController::class, 'index'])->name('documents.pending');
        Route::post('/documents/approve/{id}', [DocumentApprovalController::class, 'approve'])->name('documents.approve');
        Route::post('/documents/reject/{id}', [DocumentApprovalController::class, 'reject'])->name('documents.reject');
Route::get('/image/{id}', [DocumentController::class, 'showImage'])->name('image.show');
    });
});
});
