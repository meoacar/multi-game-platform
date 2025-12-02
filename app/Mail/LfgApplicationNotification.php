<?php

namespace App\Mail;

use App\Models\LfgApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LfgApplicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LfgApplication $application,
        public User $owner
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'LFG İlanınıza Yeni Başvuru!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lfg-application',
            with: [
                'ownerName' => $this->owner->name,
                'applicantName' => $this->application->user->name,
                'lfgTitle' => $this->application->lfgPost->title,
                'message' => $this->application->message,
                'applicationUrl' => route('lfg.applications', $this->application->lfg_post_id),
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
