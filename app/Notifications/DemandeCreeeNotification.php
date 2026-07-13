<?php

namespace App\Notifications;

use App\Models\Mission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DemandeCreeeNotification extends Notification
{
    use Queueable;

    public function __construct(public Mission $mission)
    {}

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Votre demande de service a été créée - Artilo')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Votre demande de service pour le métier **"' . $this->mission->metier_requis . '"** a bien été enregistrée.')
            ->line('Détails de votre demande :')
            ->line('- 📋 Description : ' . $this->mission->description)
            ->line('- 📍 Adresse : ' . $this->mission->adresse)
            ->line('- 📅 Date souhaitée : ' . ($this->mission->date_souhaitee ? $this->mission->date_souhaitee->format('d/m/Y') : 'Non précisée'))
            ->action('🔍 Suivre ma demande', route('particulier.mission.suivi', $this->mission))
            ->line('Nous vous informerons dès qu\'un artisan sera disponible.')
            ->line('Merci de faire confiance à Artilo !')
            ->salutation('L\'équipe Artilo');
    }

    public function toDatabase($notifiable)
    {
        return [
            'mission_id' => $this->mission->id,
            'metier' => $this->mission->metier_requis,
            'message' => 'Votre demande de service a été créée avec succès.',
            'type' => 'success'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'mission_id' => $this->mission->id,
            'metier' => $this->mission->metier_requis,
            'message' => 'Votre demande a été créée avec succès.',
            'type' => 'success'
        ]);
    }
}