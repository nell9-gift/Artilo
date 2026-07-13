<?php

namespace App\Notifications;

use App\Models\Mission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionProposeeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $mission;

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
        $url = route('artisan.missions.accepter', $this->mission->id);

        return (new MailMessage)
            ->subject('Nouvelle mission proposée - Artilo')
            ->line('Une nouvelle mission vous est proposée.')
            ->line('Description : ' . $this->mission->description)
            ->line('Métier requis : ' . ($this->mission->metier->nom ?? $this->mission->metier_requis))
            ->action('Voir la mission', $url)
            ->line('Vous avez ' . config('artilo.delai_acceptation_minutes', 20) . ' minutes pour répondre.');
    }

    public function toArray($notifiable)
    {
        return [
            'mission_id' => $this->mission->id,
            'message' => 'Nouvelle mission proposée',
            'description' => $this->mission->description,
            'expire_le' => $this->mission->expire_le,
        ];
    }
}