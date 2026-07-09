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
        Schema::create('email_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name'); // e.g., Zoho Mail SMTP, Gmail SMTP, Outlook / Microsoft 365, Amazon SES, Mailgun, SendGrid, Brevo, Custom SMTP
            $table->string('provider_slug')->unique(); // zoho, gmail, outlook, ses, mailgun, sendgrid, brevo, custom
            $table->string('host')->nullable();
            $table->integer('port')->default(587);
            $table->string('encryption')->nullable()->default('tls'); // ssl, tls, null
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // Encrypted via Crypt::encryptString()
            $table->string('from_name')->default('XrootAI');
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->string('connection_status')->default('untested'); // untested, connected, failed
            $table->text('last_error')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamps();
        });

        // Seed default providers
        $defaultProviders = [
            [
                'provider_name' => 'Zoho Mail SMTP',
                'provider_slug' => 'zoho',
                'host' => 'smtp.zoho.com',
                'port' => 465,
                'encryption' => 'ssl',
            ],
            [
                'provider_name' => 'Gmail SMTP',
                'provider_slug' => 'gmail',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'Outlook / Microsoft 365',
                'provider_slug' => 'outlook',
                'host' => 'smtp.office365.com',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'Amazon SES',
                'provider_slug' => 'ses',
                'host' => 'email-smtp.us-east-1.amazonaws.com',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'Mailgun',
                'provider_slug' => 'mailgun',
                'host' => 'smtp.mailgun.org',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'SendGrid',
                'provider_slug' => 'sendgrid',
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'Brevo (Sendinblue)',
                'provider_slug' => 'brevo',
                'host' => 'smtp-relay.brevo.com',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'provider_name' => 'Custom SMTP',
                'provider_slug' => 'custom',
                'host' => 'localhost',
                'port' => 1025,
                'encryption' => 'null',
            ]
        ];

        foreach ($defaultProviders as $provider) {
            DB::table('email_configurations')->insert(array_merge($provider, [
                'from_name' => 'XrootAI',
                'from_email' => 'noreply@xrootai.com',
                'reply_to' => 'support@xrootai.com',
                'is_active' => false,
                'is_default' => false,
                'connection_status' => 'untested',
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
        Schema::dropIfExists('email_configurations');
    }
};
