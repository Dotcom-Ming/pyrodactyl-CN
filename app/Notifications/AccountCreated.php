<?php

namespace Pterodactyl\Notifications;

use Pterodactyl\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public ?string $token = null)
    {
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
        $message = (new MailMessage())
            ->greeting(trans('notifications.account_created.greeting', ['name' => $this->user->name]))
            ->line(trans('notifications.account_created.intro', ['app' => config('app.name')]))
            ->line(trans('notifications.labels.username', ['username' => $this->user->username]))
            ->line(trans('notifications.labels.email', ['email' => $this->user->email]));

        if (!is_null($this->token)) {
            return $message->action(
                trans('notifications.account_created.setup_action'),
                url('/auth/password/reset/' . $this->token . '?email=' . urlencode($this->user->email))
            );
        }

        return $message;
    }
}
