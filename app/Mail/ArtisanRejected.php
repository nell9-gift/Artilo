<?php

namespace App\Mail;

use App\Models\Artisan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArtisanRejected extends Mailable
{
    use Queueable, SerializesModels;

    public Artisan $artisan;

    public function __construct(Artisan $artisan)
    {
        $this->artisan = $artisan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réponse à votre candidature',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.artisan-rejected',
        );
    }
}
