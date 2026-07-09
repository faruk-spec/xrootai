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
        Schema::create('oauth_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->string('provider_slug')->unique();
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('redirect_url')->nullable();
            
            // Custom provider fields
            $table->string('auth_url')->nullable();
            $table->string('token_url')->nullable();
            $table->string('user_info_url')->nullable();
            
            $table->text('scopes')->nullable(); // JSON array
            $table->text('additional_params')->nullable(); // JSON array
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Seed default providers
        $providers = [
            [
                'provider_name' => 'Google',
                'provider_slug' => 'google',
                'scopes' => json_encode(['openid', 'profile', 'email']),
            ],
            [
                'provider_name' => 'GitHub',
                'provider_slug' => 'github',
                'scopes' => json_encode(['read:user', 'user:email']),
            ],
            [
                'provider_name' => 'Facebook',
                'provider_slug' => 'facebook',
                'scopes' => json_encode(['email', 'public_profile']),
            ],
            [
                'provider_name' => 'Microsoft',
                'provider_slug' => 'microsoft',
                'scopes' => json_encode(['User.Read']),
            ],
            [
                'provider_name' => 'Apple',
                'provider_slug' => 'apple',
                'scopes' => json_encode(['name', 'email']),
            ],
            [
                'provider_name' => 'LinkedIn',
                'provider_slug' => 'linkedin',
                'scopes' => json_encode(['openid', 'profile', 'email']),
            ],
            [
                'provider_name' => 'X (Twitter)',
                'provider_slug' => 'twitter',
                'scopes' => json_encode(['users.read', 'tweet.read']),
            ],
            [
                'provider_name' => 'Discord',
                'provider_slug' => 'discord',
                'scopes' => json_encode(['identify', 'email']),
            ],
            [
                'provider_name' => 'GitLab',
                'provider_slug' => 'gitlab',
                'scopes' => json_encode(['read_user']),
            ],
            [
                'provider_name' => 'Slack',
                'provider_slug' => 'slack',
                'scopes' => json_encode(['openid', 'profile', 'email']),
            ],
            [
                'provider_name' => 'Custom OAuth Provider',
                'provider_slug' => 'custom',
                'scopes' => json_encode([]),
            ]
        ];

        foreach ($providers as $provider) {
            DB::table('oauth_providers')->insert(array_merge($provider, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_providers');
    }
};
