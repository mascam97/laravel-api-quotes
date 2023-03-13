<?php

namespace Domain\Users\Notifications;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting(trans('mail.greeting.newsletter')) /* @phpstan-ignore-line */
            ->line(trans('mail.notification.newsletter', [
                'user' => $notifiable->name,
                'user_count' => User::query()->count(),
                'quote_count' => Quote::query()->count(),
            ]))
            ->action(trans('mail.link.website'), env('APP_URL', 'http://localhost'))   /* @phpstan-ignore-line */
            ->line(trans('mail.gratitude'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(User $notifiable): array
    {
        return [

        ];
    }
}
