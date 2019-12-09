<?php

namespace App\Notifications;

use App\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class ResetPasswordEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Obnovení hesla")
            ->line('Pro obnovení hesla pokračujte níže uvedeným odkazem')
            ->action(
                "Obnovit heslo",
                $this->resetUrl($notifiable)
            )
            ->line('Thank you for using our application!');
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        // create token
        $token = app(PasswordBroker::class)->createToken(User::findOrFail($notifiable->getKey()));

        // prepare signed link
        $link = URL::temporarySignedRoute(
            'forgot.password', Carbon::now()->addMinutes(60), [
                'id' => $notifiable->getKey(),
                'token' => $token
            ]
        );

        // prepare remote link
        $remoteLink = config('frontend.url') . 'reset-password?link=' . base64_encode($link);

        return $remoteLink;
    }
}