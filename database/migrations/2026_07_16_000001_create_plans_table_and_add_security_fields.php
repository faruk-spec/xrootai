<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
     {
         if (!Schema::hasTable('plans')) {
             Schema::create('plans', function (Blueprint $table) {
                 $table->id();
                 $table->string('name');
                 $table->string('slug')->unique(); // free, pro, enterprise
                 $table->integer('daily_limit')->default(50); // daily messages
                 $table->bigInteger('monthly_token_limit')->default(500000);
                 $table->integer('max_file_size')->default(5120); // in KB (5MB default)
                 $table->json('allowed_models')->nullable(); // array of allowed model identifiers or roles
                 $table->decimal('price', 8, 2)->default(0.00);
                 $table->integer('concurrent_streams')->default(1);
                 $table->timestamps();
             });

             // Seed default plans
             DB::table('plans')->insert([
                 [
                     'name' => 'Free Plan',
                     'slug' => 'free',
                     'daily_limit' => 50,
                     'monthly_token_limit' => 500000,
                     'max_file_size' => 5120, // 5MB
                     'allowed_models' => json_encode(['gpt-4o-mini', 'gemini-1.5-flash', 'deepseek-chat', 'llama3', 'mock']),
                     'price' => 0.00,
                     'concurrent_streams' => 1,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ],
                 [
                     'name' => 'Pro Plan',
                     'slug' => 'pro',
                     'daily_limit' => 500,
                     'monthly_token_limit' => 5000000,
                     'max_file_size' => 25600, // 25MB
                     'allowed_models' => json_encode(['gpt-4o', 'gpt-4o-mini', 'claude-3-5-sonnet', 'gemini-1.5-flash', 'deepseek-chat', 'llama3', 'mock']),
                     'price' => 29.00,
                     'concurrent_streams' => 3,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ],
                 [
                     'name' => 'Enterprise Plan',
                     'slug' => 'enterprise',
                     'daily_limit' => -1, // -1 means unlimited
                     'monthly_token_limit' => -1,
                     'max_file_size' => 102400, // 100MB
                     'allowed_models' => json_encode(['*']),
                     'price' => 199.00,
                     'concurrent_streams' => 10,
                     'created_at' => now(),
                     'updated_at' => now(),
                 ]
             ]);
         }

         Schema::table('users', function (Blueprint $table) {
             if (!Schema::hasColumn('users', 'plan_id')) {
                 $table->foreignId('plan_id')->nullable()->after('id')->constrained('plans')->onDelete('set null');
             }
             if (!Schema::hasColumn('users', 'last_admin_activity_at')) {
                 $table->timestamp('last_admin_activity_at')->nullable()->after('last_login_at');
             }
         });

         Schema::table('api_keys', function (Blueprint $table) {
             if (!Schema::hasColumn('api_keys', 'last_used_at')) {
                 $table->timestamp('last_used_at')->nullable()->after('is_active');
             }
             if (!Schema::hasColumn('api_keys', 'usage_count')) {
                 $table->unsignedBigInteger('usage_count')->default(0)->after('last_used_at');
             }
         });

         Schema::table('oauth_providers', function (Blueprint $table) {
             if (!Schema::hasColumn('oauth_providers', 'last_used_at')) {
                 $table->timestamp('last_used_at')->nullable()->after('is_active');
             }
             if (!Schema::hasColumn('oauth_providers', 'usage_count')) {
                 $table->unsignedBigInteger('usage_count')->default(0)->after('last_used_at');
             }
         });

         Schema::table('activity_logs', function (Blueprint $table) {
             if (!Schema::hasColumn('activity_logs', 'old_values')) {
                 $table->json('old_values')->nullable()->after('user_agent');
             }
             if (!Schema::hasColumn('activity_logs', 'new_values')) {
                 $table->json('new_values')->nullable()->after('old_values');
             }
         });

         // Add performance and security indexes if not already indexed
         Schema::table('messages', function (Blueprint $table) {
             try {
                 $table->index('conversation_id', 'messages_conversation_id_index');
             } catch (\Throwable $e) {}
         });

         Schema::table('attachments', function (Blueprint $table) {
             try {
                 $table->index('message_id', 'attachments_message_id_index');
             } catch (\Throwable $e) {}
         });

         Schema::table('conversations', function (Blueprint $table) {
             try {
                 $table->index('user_id', 'conversations_user_id_index');
             } catch (\Throwable $e) {}
             try {
                 $table->index('session_token', 'conversations_session_token_index');
             } catch (\Throwable $e) {}
         });

         // Add soft deletes where needed
         Schema::table('users', function (Blueprint $table) {
             if (!Schema::hasColumn('users', 'deleted_at')) {
                 $table->softDeletes();
             }
         });

         Schema::table('ai_models', function (Blueprint $table) {
             if (!Schema::hasColumn('ai_models', 'deleted_at')) {
                 $table->softDeletes();
             }
         });

         Schema::table('ai_providers', function (Blueprint $table) {
             if (!Schema::hasColumn('ai_providers', 'deleted_at')) {
                 $table->softDeletes();
             }
         });
     }

     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
         Schema::table('ai_providers', function (Blueprint $table) {
             if (Schema::hasColumn('ai_providers', 'deleted_at')) {
                 $table->dropSoftDeletes();
             }
         });

         Schema::table('ai_models', function (Blueprint $table) {
             if (Schema::hasColumn('ai_models', 'deleted_at')) {
                 $table->dropSoftDeletes();
             }
         });

         Schema::table('users', function (Blueprint $table) {
             if (Schema::hasColumn('users', 'deleted_at')) {
                 $table->dropSoftDeletes();
             }
             if (Schema::hasColumn('users', 'plan_id')) {
                 $table->dropForeign(['plan_id']);
                 $table->dropColumn('plan_id');
             }
             if (Schema::hasColumn('users', 'last_admin_activity_at')) {
                 $table->dropColumn('last_admin_activity_at');
             }
         });

         Schema::table('activity_logs', function (Blueprint $table) {
             if (Schema::hasColumn('activity_logs', 'old_values')) {
                 $table->dropColumn('old_values');
             }
             if (Schema::hasColumn('activity_logs', 'new_values')) {
                 $table->dropColumn('new_values');
             }
         });

         Schema::table('oauth_providers', function (Blueprint $table) {
             if (Schema::hasColumn('oauth_providers', 'last_used_at')) {
                 $table->dropColumn(['last_used_at', 'usage_count']);
             }
         });

         Schema::table('api_keys', function (Blueprint $table) {
             if (Schema::hasColumn('api_keys', 'last_used_at')) {
                 $table->dropColumn(['last_used_at', 'usage_count']);
             }
         });

         Schema::dropIfExists('plans');
     }
};
