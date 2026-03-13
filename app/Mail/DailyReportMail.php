<?php

namespace App\Mail;

use App\Models\AnalysisArticle;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public AnalysisArticle $article;

    public function __construct(User $user, AnalysisArticle $article)
    {
        $this->user = $user;
        $this->article = $article;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 ' . $this->article->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report',
            with: [
                'user' => $this->user,
                'article' => $this->article,
                'unsubscribeUrl' => route('user.subscription.unsubscribe', ['userId' => $this->user->id, 'type' => 'email_daily_report']),
            ]
        );
    }
}
