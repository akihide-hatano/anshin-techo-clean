<?php

namespace App\Mail;

use App\Models\Medication;
use App\Models\Record;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicationUncompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Record $record,
        public Medication $medication,
        public ?string $reasonNotTaken
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '内服忘れ通知: ' . $this->user->name . 'さんの内服記録が未完了です',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.medication-uncompleted',
            with: [
                'user' => $this->user,
                'record' => $this->record,
                'medication' => $this->medication,
                'reasonNotTaken' => $this->reasonNotTaken,
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