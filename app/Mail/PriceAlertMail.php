<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $priceData;

    public function __construct(User $user, array $priceData)
    {
        $this->user = $user;
        $this->priceData = $priceData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Cảnh báo biến động giá vàng - ' . now()->format('d/m/Y H:i'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.price-alert',
            with: [
                'user' => $this->user,
                'priceData' => $this->priceData,
                'unsubscribeUrl' => route('user.subscription.unsubscribe', ['userId' => $this->user->id, 'type' => 'email_price_alerts']),
            ]
        );
    }
}
