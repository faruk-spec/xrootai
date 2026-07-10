<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public ?string $ipAddress;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?string $ipAddress = null)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress ?: request()->ip();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI');
        return new Envelope(
            subject: 'Security Alert: Your Password Was Changed — ' . $appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'auth.emails.password-reset-success',
            with: [
                'userName' => $this->user->name,
                'email' => $this->user->email,
                'appName' => \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'),
                'ipAddress' => $this->ipAddress,
                'timestamp' => now()->format('M d, Y H:i:s T'),
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
