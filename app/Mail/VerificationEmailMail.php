<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserVerification;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public UserVerification $verification;
    protected ?array $renderedTemplate = null;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, UserVerification $verification)
    {
        $this->user = $user;
        $this->verification = $verification;

        $slug = !empty($verification->otp) ? 'user_verification_otp' : 'user_verification_link';
        $this->renderedTemplate = EmailTemplate::render($slug, [
            'user_name' => $user->name,
            'otp_code' => $verification->otp ?? '',
            'verification_link' => route('verification.verify-link', ['token' => $verification->token]),
            'expiry_minutes' => $verification->expires_at->diffInMinutes(now()),
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

        return new Envelope(
            subject: 'Verify Your Email Address — ' . \App\Models\SystemSetting::get('general_chatbot_name', config('app.name', 'XrootAI')),
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
            view: 'auth.emails.verification',
            with: [
                'userName' => $this->user->name,
                'otpCode' => $this->verification->otp,
                'verificationUrl' => route('verification.verify-link', ['token' => $this->verification->token]),
                'expiryMinutes' => $this->verification->expires_at->diffInMinutes(now()),
                'appName' => \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'),
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
