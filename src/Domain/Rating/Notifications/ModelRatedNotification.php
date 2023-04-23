<?php

namespace Domain\Rating\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModelRatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $qualifierName,
        private readonly string $rateableName,
        private readonly ?int $score
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(mixed $notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting(trans('mail.greeting.quote_rated'))
            ->line(trans('mail.notification.quote_rated', [/* @phpstan-ignore-line */
                'qualifier' => $this->qualifierName,
                'quote' => $this->rateableName,
                'score' => $this->score,
            ]))
            ->action(trans('mail.link.website'), env('APP_URL', 'http://localhost'))
            ->line(trans('mail.gratitude'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(mixed $notifiable): array
    {
        return [

        ];
    }
}
