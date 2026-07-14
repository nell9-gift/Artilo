<?php

namespace App\Notifications;

use App\Models\Mission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionAccepteeNotification extends Notification
{
    use Queueable;

    protected $mission;

    public function __construct(Mission $mission)
    {
        $this->mission = $mission;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre mission a été acceptée !')
            ->greeting('Bonjour '.$notifiable->name.' !')
            ->line('Un prestataire a accepté votre mission #'.$this->mission->id)
            ->line('Description : '.$this->mission->description)
            ->action('Voir la mission', url('/particulier/demande/'.$this->mission->id))
            ->line('Le prestataire va maintenant réaliser le diagnostic.');
    }

    public function toArray($notifiable)
    {
        return [
            'mission_id' => $this->mission->id,
            'message' => 'Votre mission #'.$this->mission->id.' a été acceptée par un prestataire.',
        ];
    }
}
