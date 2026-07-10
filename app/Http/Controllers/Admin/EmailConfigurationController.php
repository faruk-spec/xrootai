<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailConfiguration;
use App\Models\ActivityLog;
use App\Http\Requests\Admin\EmailConfigurationRequest;
use App\Services\DynamicMailConfigService;
use Illuminate\Http\Request;

class EmailConfigurationController extends Controller
{
    /**
     * Display a listing of all email providers and configurations.
     */
    public function index()
    {
        $configurations = EmailConfiguration::orderBy('is_default', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.email_configurations.index', compact('configurations'));
    }

    /**
     * Show the form for editing the specified email provider.
     */
    public function edit(EmailConfiguration $emailConfiguration)
    {
        return view('admin.email_configurations.edit', compact('emailConfiguration'));
    }

    /**
     * Update the specified email configuration.
     */
    public function update(EmailConfigurationRequest $request, EmailConfiguration $emailConfiguration)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');
        $isDefault = $request->boolean('is_default');

        // Trim leading and trailing whitespace from string inputs to prevent 535 auth failures
        foreach (['host', 'username', 'password', 'from_email', 'reply_to', 'from_name'] as $field) {
            if (isset($validated[$field]) && is_string($validated[$field])) {
                $validated[$field] = trim($validated[$field]);
            }
        }

        // Do not update password field if submitted blank after trim
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $emailConfiguration->update($validated);

        if ($isDefault) {
            $emailConfiguration->makeDefault();
        } elseif ($emailConfiguration->is_default && !$isDefault) {
            $emailConfiguration->update(['is_default' => false]);
        }

        ActivityLog::log('update_email_config', "Updated SMTP configuration for provider: {$emailConfiguration->provider_name}");

        return redirect()->route('admin.email-config.index')
            ->with('success', "Email settings for {$emailConfiguration->provider_name} updated successfully.");
    }

    /**
     * Test SMTP connection socket/stream for the specified provider.
     */
    public function testConnection(EmailConfiguration $emailConfiguration)
    {
        $result = DynamicMailConfigService::testConnection($emailConfiguration);

        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', "Connection Test Failed: " . $result['message']);
        }
    }

    /**
     * Send a test verification email to the provided address using this provider.
     */
    public function sendTestEmail(Request $request, EmailConfiguration $emailConfiguration)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $recipient = $request->input('test_email');
        $result = DynamicMailConfigService::sendTestEmail($emailConfiguration, $recipient);

        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', "Failed to Send Test Email: " . $result['message']);
        }
    }

    /**
     * Reset the specified configuration back to clean state.
     */
    public function reset(EmailConfiguration $emailConfiguration)
    {
        $emailConfiguration->update([
            'username' => null,
            'password' => null,
            'is_active' => false,
            'is_default' => false,
            'connection_status' => 'untested',
            'last_error' => null,
            'last_tested_at' => null,
        ]);

        ActivityLog::log('reset_email_config', "Reset configuration for provider: {$emailConfiguration->provider_name}");

        return redirect()->route('admin.email-config.index')
            ->with('success', "Configuration for {$emailConfiguration->provider_name} has been reset.");
    }
}
