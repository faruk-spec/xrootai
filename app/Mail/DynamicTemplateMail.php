<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $slug;
    public array $data;
    protected ?array $renderedTemplate = null;

    /**
     * Create a new dynamic template mail instance.
     */
    public function __construct(string $slug, array $data = [])
    {
        $this->slug = $slug;
        $this->data = $data;
        $this->renderedTemplate = EmailTemplate::render($slug, $data);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->renderedTemplate['subject'] ?? ($this->data['subject'] ?? 'Notification — ' . config('app.name', 'XrootAI'));
        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $html = $this->renderedTemplate['body_html'] ?? ($this->data['body_html'] ?? '<p>No content provided.</p>');
        return new Content(htmlString: $html);
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
