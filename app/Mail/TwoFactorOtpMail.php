<?php

namespace App\Mail;

use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public User $user;
    protected ?array $renderedTemplate = null;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, User $user)
    {
        $this->otp = $otp;
        $this->user = $user;

        $this->renderedTemplate = EmailTemplate::render('two_factor_otp', [
            'user_name' => $user->name,
            'otp_code' => $otp,
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
            subject: 'Two-Factor Authentication Code: ' . $this->otp . ' — ' . $appName,
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
            view: 'auth.emails.two-factor-otp',
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
