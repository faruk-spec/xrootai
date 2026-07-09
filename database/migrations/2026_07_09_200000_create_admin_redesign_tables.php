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
        // 1. AI Providers
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // openai, claude, gemini, deepseek, ollama, custom, etc.
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable(); // Encrypted at runtime
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // Custom config (organization ID, default options, headers)
            $table->timestamps();
        });

        // 2. AI Models
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->onDelete('cascade');
            $table->string('name'); // e.g., Claude 3.5 Sonnet
            $table->string('model_identifier'); // e.g., claude-3-5-sonnet
            $table->string('type')->default('chat'); // chat, embedding, image, speech
            $table->integer('context_window')->default(16384);
            $table->integer('max_tokens')->default(4096);
            $table->decimal('cost_per_million_input', 8, 4)->default(0.0000);
            $table->decimal('cost_per_million_output', 8, 4)->default(0.0000);
            $table->json('capabilities')->nullable(); // e.g. {"vision": true, "image_gen": false}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. AI Routing Rules
        Schema::create('ai_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_type')->default('pattern'); // pattern, plan, fallback
            $table->string('pattern')->nullable(); // regex / keyword triggers
            $table->foreignId('target_model_id')->constrained('ai_models')->onDelete('cascade');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Prompt Templates
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('content');
            $table->json('variables')->nullable(); // JSON array of placeholders (e.g., ["user_name", "date"])
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Knowledge Bases (RAG indexing)
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('url'); // url, file, faq
            $table->text('source_path');
            $table->string('status')->default('pending'); // pending, indexed, failed
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable(); // file_size, chunk_count, etc.
            $table->timestamps();
        });

        // 6. Activity Logs (Audit logs)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create_model, update_setting, delete_user
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 7. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 8. Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 9. Role-Permission Pivot
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // 10. User-Role Pivot (linking users to roles dynamically)
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        // Seed dynamic AI Providers
        $providers = [
            [
                'name' => 'OpenAI',
                'slug' => 'openai',
                'base_url' => 'https://api.openai.com/v1',
                'is_active' => true,
                'config' => json_encode(['org_id' => null]),
            ],
            [
                'name' => 'Anthropic Claude',
                'slug' => 'claude',
                'base_url' => 'https://api.anthropic.com/v1',
                'is_active' => true,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Google Gemini',
                'slug' => 'gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'is_active' => true,
                'config' => json_encode([]),
            ],
            [
                'name' => 'DeepSeek',
                'slug' => 'deepseek',
                'base_url' => 'https://api.deepseek.com/v1',
                'is_active' => true,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Ollama',
                'slug' => 'ollama',
                'base_url' => 'http://localhost:11434/v1',
                'is_active' => true,
                'config' => json_encode([]),
            ],
            [
                'name' => 'xAI (Grok)',
                'slug' => 'xai',
                'base_url' => 'https://api.x.ai/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Groq',
                'slug' => 'groq',
                'base_url' => 'https://api.groq.com/openai/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Mistral AI',
                'slug' => 'mistral',
                'base_url' => 'https://api.mistral.ai/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'OpenRouter',
                'slug' => 'openrouter',
                'base_url' => 'https://openrouter.ai/api/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Together AI',
                'slug' => 'together',
                'base_url' => 'https://api.together.xyz/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'LM Studio',
                'slug' => 'lmstudio',
                'base_url' => 'http://localhost:1234/v1',
                'is_active' => false,
                'config' => json_encode([]),
            ],
            [
                'name' => 'Azure OpenAI',
                'slug' => 'azure',
                'base_url' => null,
                'is_active' => false,
                'config' => json_encode(['api_version' => '2024-02-15-preview']),
            ],
            [
                'name' => 'Mock Provider',
                'slug' => 'mock',
                'base_url' => null,
                'is_active' => true,
                'config' => json_encode([]),
            ],
        ];

        foreach ($providers as $provider) {
            DB::table('ai_providers')->insert(array_merge($provider, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Seed dynamic AI Models
        $openai = DB::table('ai_providers')->where('slug', 'openai')->first()->id;
        $claude = DB::table('ai_providers')->where('slug', 'claude')->first()->id;
        $gemini = DB::table('ai_providers')->where('slug', 'gemini')->first()->id;
        $deepseek = DB::table('ai_providers')->where('slug', 'deepseek')->first()->id;
        $ollama = DB::table('ai_providers')->where('slug', 'ollama')->first()->id;
        $mock = DB::table('ai_providers')->where('slug', 'mock')->first()->id;

        $models = [
            [
                'provider_id' => $openai,
                'name' => 'GPT-4o',
                'model_identifier' => 'gpt-4o',
                'type' => 'chat',
                'context_window' => 128000,
                'max_tokens' => 4096,
                'cost_per_million_input' => 5.00,
                'cost_per_million_output' => 15.00,
                'capabilities' => json_encode(['vision' => true, 'function_calling' => true]),
                'is_active' => true,
            ],
            [
                'provider_id' => $openai,
                'name' => 'GPT-4o Mini',
                'model_identifier' => 'gpt-4o-mini',
                'type' => 'chat',
                'context_window' => 128000,
                'max_tokens' => 4096,
                'cost_per_million_input' => 0.15,
                'cost_per_million_output' => 0.60,
                'capabilities' => json_encode(['vision' => true, 'function_calling' => true]),
                'is_active' => true,
            ],
            [
                'provider_id' => $claude,
                'name' => 'Claude 3.5 Sonnet',
                'model_identifier' => 'claude-3-5-sonnet',
                'type' => 'chat',
                'context_window' => 200000,
                'max_tokens' => 8192,
                'cost_per_million_input' => 3.00,
                'cost_per_million_output' => 15.00,
                'capabilities' => json_encode(['vision' => true, 'function_calling' => true]),
                'is_active' => true,
            ],
            [
                'provider_id' => $gemini,
                'name' => 'Gemini 1.5 Flash',
                'model_identifier' => 'gemini-1.5-flash',
                'type' => 'chat',
                'context_window' => 1000000,
                'max_tokens' => 8192,
                'cost_per_million_input' => 0.35,
                'cost_per_million_output' => 1.05,
                'capabilities' => json_encode(['vision' => true, 'function_calling' => true]),
                'is_active' => true,
            ],
            [
                'provider_id' => $deepseek,
                'name' => 'DeepSeek Chat (V3)',
                'model_identifier' => 'deepseek-chat',
                'type' => 'chat',
                'context_window' => 64000,
                'max_tokens' => 8192,
                'cost_per_million_input' => 0.14,
                'cost_per_million_output' => 0.28,
                'capabilities' => json_encode(['vision' => false, 'function_calling' => true]),
                'is_active' => true,
            ],
            [
                'provider_id' => $ollama,
                'name' => 'Llama 3 (Ollama)',
                'model_identifier' => 'llama3',
                'type' => 'chat',
                'context_window' => 8192,
                'max_tokens' => 2048,
                'cost_per_million_input' => 0.00,
                'cost_per_million_output' => 0.00,
                'capabilities' => json_encode(['vision' => false, 'function_calling' => false]),
                'is_active' => true,
            ],
            [
                'provider_id' => $mock,
                'name' => 'Mock Chat Model',
                'model_identifier' => 'mock',
                'type' => 'chat',
                'context_window' => 8192,
                'max_tokens' => 2048,
                'cost_per_million_input' => 0.00,
                'cost_per_million_output' => 0.00,
                'capabilities' => json_encode(['vision' => false, 'function_calling' => false]),
                'is_active' => true,
            ],
        ];

        foreach ($models as $model) {
            DB::table('ai_models')->insert(array_merge($model, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Seed Roles & Permissions
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Unrestricted access to all modules and configurations.'],
            ['name' => 'Admin', 'description' => 'Manage system settings, AI providers, and prompts.'],
            ['name' => 'Manager', 'description' => 'Oversee user analytics, logs, and support interactions.'],
            ['name' => 'Support Agent', 'description' => 'Intervene in handoff queues and reply to chats.'],
            ['name' => 'Developer', 'description' => 'Manage API keys and integration webhooks.'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, ['created_at' => now(), 'updated_at' => now()]));
        }

        $permissions = [
            ['name' => 'manage-users', 'description' => 'Create, edit, and delete users.'],
            ['name' => 'manage-ai', 'description' => 'Manage AI Providers, models, and custom routing.'],
            ['name' => 'manage-prompts', 'description' => 'Configure system prompts and prompt templates.'],
            ['name' => 'manage-kb', 'description' => 'Sync files and manage vector RAG sources.'],
            ['name' => 'view-analytics', 'description' => 'Access system usage and cost graphs.'],
            ['name' => 'manage-settings', 'description' => 'Update SaaS limits, security, and integration parameters.'],
            ['name' => 'view-logs', 'description' => 'Audit system access logs and error monitoring.'],
            ['name' => 'human-handoff', 'description' => 'Intercept and overtake AI conversations.'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insert(array_merge($perm, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Map Permissions to Admin & Super Admin Role
        $superAdminId = DB::table('roles')->where('name', 'Super Admin')->first()->id;
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->first()->id;
        $allPermIds = DB::table('permissions')->pluck('id');

        foreach ($allPermIds as $permId) {
            DB::table('role_permission')->insert(['role_id' => $superAdminId, 'permission_id' => $permId]);
            DB::table('role_permission')->insert(['role_id' => $adminRoleId, 'permission_id' => $permId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('knowledge_bases');
        Schema::dropIfExists('prompt_templates');
        Schema::dropIfExists('ai_routing_rules');
        Schema::dropIfExists('ai_models');
        Schema::dropIfExists('ai_providers');
    }
};
