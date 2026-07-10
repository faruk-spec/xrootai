<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\EmailConfiguration;
use App\Models\ActivityLog;
use App\Mail\DynamicTemplateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the email templates.
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('name')->paginate(15);

        return view('admin.email_templates.index', compact('templates'));
    }

    /**
     * Seed or restore all default system email templates.
     */
    public function seedDefaults()
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'EmailTemplateSeeder', '--force' => true]);

        ActivityLog::log(
            'email_templates_seeded',
            'Seeded/restored default system email templates',
            Auth::id()
        );

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'All default system email templates have been seeded and restored successfully!');
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email_templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $emailTemplate->update($validated);

        ActivityLog::log(
            'email_template_updated',
            "Updated email template: {$emailTemplate->name} ({$emailTemplate->slug})",
            Auth::id()
        );

        return redirect()->route('admin.email-templates.index')
            ->with('success', "Email template '{$emailTemplate->name}' updated successfully.");
    }

    /**
     * Toggle the active status of an email template.
     */
    public function toggle(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

        $status = $emailTemplate->is_active ? 'Activated' : 'Disabled';
        ActivityLog::log(
            'email_template_toggled',
            "{$status} email template: {$emailTemplate->name}",
            Auth::id()
        );

        return back()->with('success', "Template '{$emailTemplate->name}' has been {$status}.");
    }

    /**
     * Display a live visual preview of the rendered email template.
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        $dummyData = $this->getDummyVariables($emailTemplate);
        $rendered = EmailTemplate::render($emailTemplate->slug, $dummyData);

        return view('admin.email_templates.preview', compact('emailTemplate', 'rendered', 'dummyData'));
    }

    /**
     * Send a live test email of this template to the admin or specified recipient.
     */
    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $activeConfig = EmailConfiguration::getActive();
        if (!$activeConfig) {
            return back()->with('error', 'No active SMTP / Email Provider configuration found. Please configure an email provider in Email Configurations first.');
        }

        // Configure dynamic mailer runtime
        $activeConfig->applyToRuntime();

        $dummyData = $this->getDummyVariables($emailTemplate);

        try {
            Mail::to($request->test_email)->send(new DynamicTemplateMail($emailTemplate->slug, $dummyData));

            ActivityLog::log(
                'email_template_test_sent',
                "Sent test email for template '{$emailTemplate->name}' to {$request->test_email}",
                Auth::id()
            );

            return back()->with('success', "Test email sent successfully to {$request->test_email}!");
        } catch (\Exception $e) {
            return back()->with('error', "Failed sending test email: " . $e->getMessage());
        }
    }

    /**
     * Generate realistic dummy variables for rendering previews and test emails.
     */
    protected function getDummyVariables(EmailTemplate $template): array
    {
        $data = [
            'user_name' => Auth::user()?->name ?? 'Faruque Ahmed',
            'otp_code' => '849201',
            'reset_code' => '592038',
            'verification_link' => route('login') . '?token=mock_preview_token_sample_12345',
            'reset_link' => route('login') . '?reset=mock_preview_token_sample_67890',
            'expiry_minutes' => '15',
            'ip_address' => request()->ip() ?? '192.168.1.100',
            'app_name' => \App\Models\SystemSetting::get('general_chatbot_name', config('app.name', 'XrootAI')),
            'app_url' => config('app.url', 'http://localhost'),
            'current_year' => date('Y'),
        ];

        return $data;
    }
}
