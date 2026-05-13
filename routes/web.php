<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminDocumentReviewController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// ========== GUEST ROUTES ==========
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ========== AUTHENTICATED ROUTES ==========
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('dashboard');
Route::get('/category/{name}', [DocumentController::class, 'categoryShow'])->name('category.show');
    // Document & Content Creation
    Route::get('/content/create', [DocumentController::class, 'createContent'])->name('content.create');
    Route::post('/content/store', [DocumentController::class, 'storeContent'])->name('content.store');
    Route::post('/content/link', [DocumentController::class, 'storeLink'])->name('content.link');

    // Legacy upload (optional, keep for compatibility)
    Route::get('/upload', [DocumentController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Document actions
    Route::get('/documents/edit/{id}', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/my-uploads', [DocumentController::class, 'myUploads'])->name('documents.my-uploads');
    Route::post('/document/{id}/submit-approval', [DocumentController::class, 'submitForApproval'])->name('document.submit');

    // Document viewing & interactions
    Route::get('/document/{id}', [DocumentController::class, 'show'])->name('document.show');
    Route::post('/document/{id}/bookmark', [DocumentController::class, 'toggleBookmark']);
    Route::post('/document/{id}/like', [DocumentController::class, 'toggleDocumentLike']);
    Route::get('/download/{id}', [DocumentController::class, 'download'])->name('download');
    Route::get('/download/attachment/{id}', [DocumentController::class, 'download'])->name('download.attachment');
    Route::get('/preview/{id}', [DocumentController::class, 'preview'])->name('preview');
    Route::get('/images/{id}', [DocumentController::class, 'getImage'])->name('images.show');

    // Dynamic filtering (AJAX)
    Route::get('/documents/fetch', [DocumentController::class, 'fetchDocuments'])->name('documents.fetch');

    // Search
    Route::get('/search', [DocumentController::class, 'search'])->name('search');
    Route::get('/search/autocomplete', [DocumentController::class, 'autocomplete'])->name('search.autocomplete');

    // Comments & Likes
    Route::post('/document/{docId}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/comment/{id}/like', [CommentController::class, 'like'])->name('comment.like');

    // Questions
    Route::get('/ask', [QuestionController::class, 'create'])->name('question.create');
    Route::post('/ask', [QuestionController::class, 'store'])->name('question.store');

Route::get('/category/{name}', [DocumentController::class, 'categoryShow'])->name('category.show');


    // ========== ADMIN‑ONLY ROUTES ==========
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // User management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users');
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        // Category management
        Route::resource('categories', CategoryManagementController::class);

        // Analytics dashboard
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    });

    // ========== ADMIN & KM CHAMPION ROUTES (Approvals) ==========
    Route::middleware('admin-or-kmchampion')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/pending-documents', [AdminDocumentReviewController::class, 'pending'])->name('documents.pending');
        Route::get('/review-document/{id}', [AdminDocumentReviewController::class, 'show'])->name('documents.review');
        Route::post('/review-document/{id}/comment', [AdminDocumentReviewController::class, 'addComment'])->name('documents.approval.comment');
        Route::post('/documents/approve/{id}', [AdminDocumentReviewController::class, 'approve'])->name('documents.approve');
        Route::post('/documents/reject/{id}', [AdminDocumentReviewController::class, 'reject'])->name('documents.reject');
    });

});
