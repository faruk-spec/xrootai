<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Chat Interface Routes
    Route::get('/', [ChatController::class, 'index'])->name('chat');
    Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
    Route::get('/chats/{conversation:uuid}', [ChatController::class, 'show'])->name('chats.show');
    Route::delete('/chats/{conversation:uuid}', [ChatController::class, 'destroy'])->name('chats.destroy');
    Route::patch('/chats/{conversation:uuid}/rename', [ChatController::class, 'rename'])->name('chats.rename');
    Route::post('/chats/{conversation:uuid}/pin', [ChatController::class, 'pin'])->name('chats.pin');

    // SSE Streaming
    Route::get('/chats/{conversation:uuid}/stream', [StreamController::class, 'stream'])->name('chats.stream');

    // Attachments Upload
    Route::post('/attachments/upload', [ChatController::class, 'uploadAttachment'])->name('attachments.upload');

    // Settings & Profile
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/keys', [SettingsController::class, 'updateKeys'])->name('settings.keys');
});
