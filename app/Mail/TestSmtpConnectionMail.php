<?php

namespace App\Mail;

use App\Models\EmailConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestSmtpConnectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public EmailConfiguration $config;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->config->provider_name . '] SMTP Connection Verification - ' . config('app.name', 'XrootAI'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.email_configurations.mails.test_connection',
            with: [
                'providerName' => $this->config->provider_name,
                'host' => $this->config->host,
                'port' => $this->config->port,
                'encryption' => $this->config->encryption,
                'fromEmail' => $this->config->from_email,
                'timestamp' => now()->format('Y-m-d H:i:s T'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
