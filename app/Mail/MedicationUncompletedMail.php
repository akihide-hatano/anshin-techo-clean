<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        protected $record, // ① 内服記録の情報をコンストラクタで受け取る
        protected $user // ② 患者のユーザー情報をコンストラクタで受け取る
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // ③ メール件名を設定
        return new Envelope(
            subject: '内服忘れ通知: ' . $this->user->name . 'さんの内服記録が未完了です',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // ④ メール本文をBladeテンプレートで定義
        return new Content(
            view: 'emails.medication-uncompleted',
            with: [
                'record' => $this->record,
                'user' => $this->user,
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