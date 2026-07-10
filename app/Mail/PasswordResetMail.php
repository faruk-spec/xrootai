<?php

namespace App\Mail;

use App\Models\User;
use App\Models\PasswordResetCode;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public PasswordResetCode $resetCode;
    protected ?array $renderedTemplate = null;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, PasswordResetCode $resetCode)
    {
        $this->user = $user;
        $this->resetCode = $resetCode;

        $slug = !empty($resetCode->otp) ? 'password_reset_otp' : 'password_reset_link';
        $this->renderedTemplate = EmailTemplate::render($slug, [
            'user_name' => $user->name,
            'reset_code' => $resetCode->otp ?? '',
            'reset_link' => route('password.reset', ['token' => $resetCode->token, 'email' => $user->email]),
            'expiry_minutes' => abs($resetCode->expires_at->diffInMinutes(now())),
            'ip_address' => $resetCode->ip_address,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        if ($this->renderedTemplate) {
            return new Envelope(subject: $this->renderedTemplate['subject']);
        }

        $appName = \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI');
        return new Envelope(
            subject: 'Reset Your Password — ' . $appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->renderedTemplate) {
            return new Content(htmlString: $this->renderedTemplate['body_html']);
        }

        return new Content(
            view: 'auth.emails.password-reset',
            with: [
                'userName' => $this->user->name,
                'otpCode' => $this->resetCode->otp,
                'resetUrl' => route('password.reset', ['token' => $this->resetCode->token, 'email' => $this->user->email]),
                'expiryMinutes' => abs($this->resetCode->expires_at->diffInMinutes(now())),
                'appName' => \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'),
                'ipAddress' => $this->resetCode->ip_address,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
