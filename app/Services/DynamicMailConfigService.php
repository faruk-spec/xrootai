<?php

namespace App\Services;

use App\Models\EmailConfiguration;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class DynamicMailConfigService
{
    /**
     * Dynamically configure the Laravel mail settings from the database.
     */
    public static function configure(?EmailConfiguration $config = null): bool
    {
        $config = $config ?: EmailConfiguration::getActiveDefault();

        if (!$config || empty($config->host)) {
            return false;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $config->host);
        Config::set('mail.mailers.smtp.port', $config->port);
        Config::set('mail.mailers.smtp.encryption', $config->encryption === 'null' ? null : $config->encryption);
        Config::set('mail.mailers.smtp.username', $config->username);
        Config::set('mail.mailers.smtp.password', $config->password);
        
        if (!empty($config->from_email)) {
            Config::set('mail.from.address', $config->from_email);
            Config::set('mail.from.name', $config->from_name ?: 'XrootAI');
        }

        return true;
    }

    /**
     * Test the SMTP connection by checking socket connection and transport response.
     */
    public static function testConnection(EmailConfiguration $config): array
    {
        if (empty($config->host)) {
            $config->update([
                'connection_status' => 'failed',
                'last_error' => 'SMTP Host is not configured.',
                'last_tested_at' => now(),
            ]);
            return ['status' => false, 'message' => 'SMTP Host is not configured.'];
        }

        try {
            // Socket timeout check
            $timeout = 10;
            $scheme = ($config->encryption === 'ssl') ? 'ssl://' : '';
            $targetHost = $scheme . $config->host;
            
            $socket = @fsockopen($targetHost, $config->port, $errno, $errstr, $timeout);

            if (!$socket && $config->encryption === 'tls') {
                $socket = @fsockopen($config->host, $config->port, $errno, $errstr, $timeout);
            }

            if (!$socket) {
                $errorMsg = "Could not connect to {$config->host}:{$config->port} ($errstr [$errno])";
                $config->update([
                    'connection_status' => 'failed',
                    'last_error' => $errorMsg,
                    'last_tested_at' => now(),
                ]);
                ActivityLog::log('test_smtp_failed', "SMTP test failed for {$config->provider_name}: {$errorMsg}");
                return ['status' => false, 'message' => $errorMsg];
            }

            // Read initial SMTP server banner
            $response = fgets($socket, 515);
            fclose($socket);

            // If username and password are provided, perform real SMTP authentication check via Symfony Transport
            if (!empty($config->username) && !empty($config->password)) {
                try {
                    self::configure($config);
                    // Force refresh or creation of smtp transport instance
                    $mailer = app('mail.manager')->mailer('smtp');
                    $transport = $mailer->getSymfonyTransport();
                    if (method_exists($transport, 'start')) {
                        $transport->start();
                    }
                    if (method_exists($transport, 'stop')) {
                        $transport->stop();
                    }
                } catch (Exception $authEx) {
                    $errorMsg = "SMTP Connected, but Authentication Failed: " . $authEx->getMessage();
                    $config->update([
                        'connection_status' => 'failed',
                        'last_error' => $errorMsg,
                        'last_tested_at' => now(),
                    ]);
                    ActivityLog::log('test_smtp_failed', "SMTP auth check failed for {$config->provider_name}: {$errorMsg}");
                    return ['status' => false, 'message' => $errorMsg];
                }
            }

            $successMessage = !empty($config->username)
                ? "Successfully connected and verified SMTP login for {$config->username} on {$config->host}:{$config->port}."
                : "Successfully connected socket to {$config->host}:{$config->port}. Server response: " . trim((string)$response);

            $config->update([
                'connection_status' => 'connected',
                'last_error' => null,
                'last_tested_at' => now(),
            ]);
            ActivityLog::log('test_smtp_success', "SMTP connection and verification successful for {$config->provider_name}");
            return ['status' => true, 'message' => $successMessage];
        } catch (Exception $e) {
            $config->update([
                'connection_status' => 'failed',
                'last_error' => $e->getMessage(),
                'last_tested_at' => now(),
            ]);
            ActivityLog::log('test_smtp_failed', "SMTP exception for {$config->provider_name}: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send a test email using the specified configuration.
     */
    public static function sendTestEmail(EmailConfiguration $config, string $recipientEmail): array
    {
        self::configure($config);

        try {
            Mail::to($recipientEmail)->send(new \App\Mail\TestSmtpConnectionMail($config));

            $config->update([
                'connection_status' => 'connected',
                'last_error' => null,
                'last_tested_at' => now(),
            ]);
            ActivityLog::log('send_test_email', "Test email sent via {$config->provider_name} to {$recipientEmail}");
            return ['status' => true, 'message' => "Test email successfully sent to {$recipientEmail} via {$config->provider_name}."];
        } catch (Exception $e) {
            $config->update([
                'connection_status' => 'failed',
                'last_error' => $e->getMessage(),
                'last_tested_at' => now(),
            ]);
            ActivityLog::log('send_test_email_failed', "Failed sending test email to {$recipientEmail} via {$config->provider_name}: " . $e->getMessage());
            return ['status' => false, 'message' => "Failed sending test email: " . $e->getMessage()];
        }
    }
}
