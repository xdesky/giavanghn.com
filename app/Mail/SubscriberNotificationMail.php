<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
        public string $emailSubject,
        public string $emailContent,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscriber-notification',
            with: [
                'subscriber' => $this->subscriber,
                'emailContent' => $this->emailContent,
                'unsubscribeUrl' => url('/unsubscribe/' . $this->subscriber->unsubscribe_token),
            ]
        );
    }
}
