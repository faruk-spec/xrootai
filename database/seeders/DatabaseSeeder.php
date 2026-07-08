<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        \App\Models\UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => 'system',
                'default_model' => 'mock',
                'system_prompt' => 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.',
            ]
        );
    }
}
