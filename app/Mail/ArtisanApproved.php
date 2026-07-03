<?php

namespace App\Mail;

use App\Models\Artisan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArtisanApproved extends Mailable
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
            subject: 'Votre candidature a été validée !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.artisan-approved',
        );
    }
}
