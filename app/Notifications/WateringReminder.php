<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WateringReminder extends Notification
{
    use Queueable;

    protected $plantName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $plantName)
    {
        $this->plantName = $plantName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel d\'arrosage pour votre plante')
            ->line("Il est temps d'arroser votre plante : {$this->plantName}.")
            ->action('Voir ma plante', url('/'))
            ->line('Merci d\'utiliser notre application pour prendre soin de vos plantes !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plantName' => $this->plantName,
        ];
    }
}
