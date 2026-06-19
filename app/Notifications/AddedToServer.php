<?php

namespace Pterodactyl\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AddedToServer extends Notification implements ShouldQueue
{
    use Queueable;

    public object $server;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $server)
    {
        $this->server = (object) $server;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->greeting(trans('notifications.added_to_server.greeting', ['name' => $this->server->user]))
            ->line(trans('notifications.added_to_server.intro'))
            ->line(trans('notifications.labels.server_name', ['name' => $this->server->name]))
            ->action(trans('notifications.added_to_server.action'), url('/server/' . $this->server->uuidShort));
    }
}
