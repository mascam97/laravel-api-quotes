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
     * @return array<string>
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
            ->greeting(trans('mail.greeting.newsletter'))
            ->line(trans('mail.notification.newsletter', [
                'user' => $notifiable->name,
                'user_count' => User::query()->count(),
                'quote_count' => Quote::query()->count(),
            ]))
            ->action(trans('mail.link.website'), (string) env('APP_URL', 'http://localhost'))
            ->line(trans('mail.gratitude'));
    }

    /**
     * Get the array representation of the notification.
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [

        ];
    }
}
