<?php

namespace App\Notifications;

use App\Models\User;
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
        return (new MailMessage())
            ->subject('Obnovení hesla')
            ->view('emails.reset-password', [
                'reset_link' => $this->resetUrl($notifiable),
            ]);
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        // get user
        $user = User::findOrFail($notifiable->getKey());

        // prepare signed link
        $link = URL::temporarySignedRoute(
            'reset.password',
            Carbon::now()->addMinutes(60),
            [
                'email' => $user->email,
                'token' => app(PasswordBroker::class)->createToken($user),
            ]
        );

        // prepare remote link
        $remoteLink =
            config('frontend.url') .
            'reset-password?link=' .
            base64_encode($link);

        return $remoteLink;
    }
}
