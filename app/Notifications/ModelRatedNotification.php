<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ModelRatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $rateableName;

    private float $score;

    private string $qualifierName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        string $qualifierName,
        string $rateableName,
        float $score
    ) {
        $this->qualifierName = $qualifierName;
        $this->rateableName = $rateableName;
        $this->score = $score;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting(trans('mail.greeting.quote_rated'))
            ->line(trans('mail.notification.quote_rated', [
                'qualifier' => $this->qualifierName,
                'quote' => $this->rateableName,
                'score' => $this->score,
            ]))
            ->action(trans('mail.link.website'), env('APP_URL', 'http://localhost'))
            ->line(trans('mail.gratitude'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
