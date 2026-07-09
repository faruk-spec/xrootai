<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Guest-only authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Social Login Routes
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('auth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('auth.callback');
});

// Authenticated-only routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Settings & Profile (authenticated only)
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/keys', [SettingsController::class, 'updateKeys'])->name('settings.keys');
});

// Admin Panel Routes (protected by auth and admin role check middleware)
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.role');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});

// Open Chat Interface Routes (Access allowed for both guests and authenticated users)
Route::get('/', [ChatController::class, 'index'])->name('chat');
Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
Route::get('/chats/{conversation:uuid}', [ChatController::class, 'show'])->name('chats.show');
Route::delete('/chats/{conversation:uuid}', [ChatController::class, 'destroy'])->name('chats.destroy');
Route::patch('/chats/{conversation:uuid}/rename', [ChatController::class, 'rename'])->name('chats.rename');
Route::post('/chats/{conversation:uuid}/pin', [ChatController::class, 'pin'])->name('chats.pin');

// SSE Streaming over POST (to support unlimited size prompts without URL truncation lag)
Route::post('/chats/{conversation:uuid}/stream', [StreamController::class, 'stream'])->name('chats.stream');

// Attachments Upload (open to allow quick guest attachments)
Route::post('/attachments/upload', [ChatController::class, 'uploadAttachment'])->name('attachments.upload');
