<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $weeklyData;

    public function __construct(User $user, array $weeklyData)
    {
        $this->user = $user;
        $this->weeklyData = $weeklyData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📈 Báo cáo tuần giá vàng - Tuần ' . now()->weekOfYear . '/' . now()->year,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-report',
            with: [
                'user' => $this->user,
                'weeklyData' => $this->weeklyData,
                'unsubscribeUrl' => route('user.subscription.unsubscribe', ['userId' => $this->user->id, 'type' => 'email_weekly_report']),
            ]
        );
    }
}
