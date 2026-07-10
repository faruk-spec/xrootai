<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email'); // Target email being verified
            $table->string('type')->default('email_verification'); // email_verification, password_reset, login_2fa, change_email
            $table->string('token')->unique(); // Secure token for link verification
            $table->string('otp', 32); // OTP string/code
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type', 'is_verified']);
            $table->index(['email', 'otp', 'is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
