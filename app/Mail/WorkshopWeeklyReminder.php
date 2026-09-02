<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopWeeklyReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<array<string, mixed>>  $reminders
     */
    public function __construct(public User $user, public array $reminders) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '3x30 : ta semaine');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.workshop-weekly');
    }
}
