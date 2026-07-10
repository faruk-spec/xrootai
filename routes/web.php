<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\EmailConfigurationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AuthSettingController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ProfileSecurityController;
use Illuminate\Support\Facades\Route;

// Guest-only authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Password Reset Routes
    Route::get('/password/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp');
    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordResetController::class, 'update'])->name('password.update');
});

// Two-Factor Authentication (2FA) Challenge Routes (unauthenticated/pending login session check)
Route::get('/two-factor-challenge', [TwoFactorController::class, 'showChallengeForm'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');
Route::post('/two-factor-challenge/resend', [TwoFactorController::class, 'resendOtp'])->name('two-factor.resend');
Route::post('/two-factor-challenge/cancel', [TwoFactorController::class, 'cancelChallenge'])->name('two-factor.cancel');


// Authenticated-only routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Profile & Two-Factor Security Management
    Route::get('/profile/security', [ProfileSecurityController::class, 'index'])->name('profile.security');
    Route::post('/profile/security', [ProfileSecurityController::class, 'update'])->name('profile.security.update');
    Route::post('/profile/security/totp-confirm', [ProfileSecurityController::class, 'confirmTotp'])->name('profile.security.totp-confirm');

    // Email Verification Routes
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify/otp', [EmailVerificationController::class, 'verifyOtp'])->name('verification.verify-otp');
    Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verifyLink'])->name('verification.verify-link');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::post('/email/change', [EmailVerificationController::class, 'changeEmail'])->name('verification.change-email');

    // Settings & Profile (authenticated only)
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/keys', [SettingsController::class, 'updateKeys'])->name('settings.keys');
});

// Admin Panel Routes (protected by auth and admin role check middleware)
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // User Directory CRUD
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{user}/role', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.role'); // Mapping to update for edit form submission
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.delete');

    // Role Management (RBAC)
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    // Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    
    // Admin User Verification & Approval Overrides
    Route::post('/users/{user}/verify-manually', [App\Http\Controllers\Admin\UserController::class, 'verifyManually'])->name('admin.users.verify-manually');
    Route::post('/users/{user}/resend-verification', [App\Http\Controllers\Admin\UserController::class, 'resendVerification'])->name('admin.users.resend-verification');
    Route::post('/users/{user}/approve', [App\Http\Controllers\Admin\UserController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/users/{user}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspendUser'])->name('admin.users.suspend');
    
    // System Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/permissions', [App\Http\Controllers\Admin\SettingController::class, 'updatePermissions'])->name('admin.settings.permissions');

    // AI Providers CRUD
    Route::get('/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('admin.providers.index');
    Route::get('/providers/{provider}/edit', [App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('admin.providers.edit');
    Route::put('/providers/{provider}', [App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('admin.providers.update');
    Route::post('/providers/{provider}/test', [App\Http\Controllers\Admin\ProviderController::class, 'testConnection'])->name('admin.providers.test');

    // AI Models CRUD
    Route::get('/models', [App\Http\Controllers\Admin\ModelController::class, 'index'])->name('admin.models.index');
    Route::get('/models/create', [App\Http\Controllers\Admin\ModelController::class, 'create'])->name('admin.models.create');
    Route::post('/models', [App\Http\Controllers\Admin\ModelController::class, 'store'])->name('admin.models.store');
    Route::get('/models/{model}/edit', [App\Http\Controllers\Admin\ModelController::class, 'edit'])->name('admin.models.edit');
    Route::put('/models/{model}', [App\Http\Controllers\Admin\ModelController::class, 'update'])->name('admin.models.update');
    Route::delete('/models/{model}', [App\Http\Controllers\Admin\ModelController::class, 'destroy'])->name('admin.models.delete');

    // AI Routing CRUD
    Route::get('/routing', [App\Http\Controllers\Admin\RoutingController::class, 'index'])->name('admin.routing.index');
    Route::get('/routing/create', [App\Http\Controllers\Admin\RoutingController::class, 'create'])->name('admin.routing.create');
    Route::post('/routing', [App\Http\Controllers\Admin\RoutingController::class, 'store'])->name('admin.routing.store');
    Route::get('/routing/{routing}/edit', [App\Http\Controllers\Admin\RoutingController::class, 'edit'])->name('admin.routing.edit');
    Route::put('/routing/{routing}', [App\Http\Controllers\Admin\RoutingController::class, 'update'])->name('admin.routing.update');
    Route::delete('/routing/{routing}', [App\Http\Controllers\Admin\RoutingController::class, 'destroy'])->name('admin.routing.delete');

    // Prompt Templates CRUD
    Route::get('/prompts', [App\Http\Controllers\Admin\PromptController::class, 'index'])->name('admin.prompts.index');
    Route::get('/prompts/create', [App\Http\Controllers\Admin\PromptController::class, 'create'])->name('admin.prompts.create');
    Route::post('/prompts', [App\Http\Controllers\Admin\PromptController::class, 'store'])->name('admin.prompts.store');
    Route::get('/prompts/{prompt}/edit', [App\Http\Controllers\Admin\PromptController::class, 'edit'])->name('admin.prompts.edit');
    Route::put('/prompts/{prompt}', [App\Http\Controllers\Admin\PromptController::class, 'update'])->name('admin.prompts.update');
    Route::delete('/prompts/{prompt}', [App\Http\Controllers\Admin\PromptController::class, 'destroy'])->name('admin.prompts.delete');

    // Knowledge Base (RAG) CRUD
    Route::get('/kb', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'index'])->name('admin.kb.index');
    Route::get('/kb/create', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'create'])->name('admin.kb.create');
    Route::post('/kb', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'store'])->name('admin.kb.store');
    Route::post('/kb/{kb}/sync', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'sync'])->name('admin.kb.sync');
    Route::delete('/kb/{kb}', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'destroy'])->name('admin.kb.delete');

    // Conversations Log Audit
    Route::get('/conversations', [App\Http\Controllers\Admin\ConversationController::class, 'index'])->name('admin.conversations.index');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\Admin\ConversationController::class, 'show'])->name('admin.conversations.show');
    Route::delete('/conversations/{conversation}', [App\Http\Controllers\Admin\ConversationController::class, 'destroy'])->name('admin.conversations.delete');

    // OAuth Configurations CRUD
    Route::get('/oauth', [App\Http\Controllers\Admin\OAuthProviderController::class, 'index'])->name('admin.oauth.index');
    Route::put('/oauth/{provider}', [App\Http\Controllers\Admin\OAuthProviderController::class, 'update'])->name('admin.oauth.update');
    Route::post('/oauth/{provider}/reset', [App\Http\Controllers\Admin\OAuthProviderController::class, 'reset'])->name('admin.oauth.reset');
    Route::post('/oauth/{provider}/test', [App\Http\Controllers\Admin\OAuthProviderController::class, 'testConnection'])->name('admin.oauth.test');

    // Audit Logs
    Route::get('/logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.logs.index');

    // Email Configuration Module
    Route::get('/email-config', [EmailConfigurationController::class, 'index'])->name('admin.email-config.index');
    Route::get('/email-config/{email_configuration}/edit', [EmailConfigurationController::class, 'edit'])->name('admin.email-config.edit');
    Route::put('/email-config/{email_configuration}', [EmailConfigurationController::class, 'update'])->name('admin.email-config.update');
    Route::post('/email-config/{email_configuration}/test', [EmailConfigurationController::class, 'testConnection'])->name('admin.email-config.test');
    Route::post('/email-config/{email_configuration}/send-test', [EmailConfigurationController::class, 'sendTestEmail'])->name('admin.email-config.send-test');
    Route::post('/email-config/{email_configuration}/reset', [EmailConfigurationController::class, 'reset'])->name('admin.email-config.reset');

    // Authentication & Security Settings Module
    Route::get('/auth-settings', [AuthSettingController::class, 'index'])->name('admin.auth-settings.index');
    Route::post('/auth-settings', [AuthSettingController::class, 'update'])->name('admin.auth-settings.update');

    // Email Template Management Module
    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('admin.email-templates.index');
    Route::get('/email-templates/{email_template}/edit', [EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{email_template}', [EmailTemplateController::class, 'update'])->name('admin.email-templates.update');
    Route::post('/email-templates/{email_template}/toggle', [EmailTemplateController::class, 'toggle'])->name('admin.email-templates.toggle');
    Route::get('/email-templates/{email_template}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
    Route::post('/email-templates/{email_template}/test', [EmailTemplateController::class, 'sendTest'])->name('admin.email-templates.test');
});

// Legal & Privacy Pages
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

// Open Chat Interface Routes (Access allowed for both guests and authenticated users)
Route::get('/', [ChatController::class, 'index'])->name('chat');
Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
Route::get('/chats/{conversation:uuid}', [ChatController::class, 'show'])->name('chats.show');
Route::delete('/chats/{conversation:uuid}', [ChatController::class, 'destroy'])->name('chats.destroy');
Route::patch('/chats/{conversation:uuid}/rename', [ChatController::class, 'rename'])->name('chats.rename');
Route::post('/chats/{conversation:uuid}/pin', [ChatController::class, 'pin'])->name('chats.pin');

// SSE Streaming over POST (to support unlimited size prompts without URL truncation lag)
Route::post('/chats/{conversation:uuid}/stream', [StreamController::class, 'stream'])->name('chats.stream');

// Attachments Upload (open to allow quick guest attachments)
Route::post('/attachments/upload', [ChatController::class, 'uploadAttachment'])->name('attachments.upload');

// Social Authentication redirect and callback routes
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\OAuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\OAuthController::class, 'callback'])->name('auth.callback');
