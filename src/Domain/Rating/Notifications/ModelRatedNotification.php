<?php

namespace Domain\Rating\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ModelRatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $qualifierId,
        private readonly string $qualifierName,
        private readonly string $rateableName,
        private readonly ?int $score
    ) {
    }

    /**
     * @return array<string>
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
            ->action(trans('mail.link.website'), (string) env('APP_URL', 'http://localhost'))
            ->action(
                trans('mail.link.unsubscribe'),
                URL::signedRoute('web.email-unsubscribe-users', ['user' => $this->qualifierId])
            )
            ->line(trans('mail.gratitude'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [

        ];
    }
}
