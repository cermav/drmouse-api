<?php

namespace App\Notifications;

use App\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        return (new MailMessage())
            ->subject(Lang::getFromJson('Oveření registračního emailu'))
            ->greeting("Dobrý den,")
            ->line("prosím, potvrďte níže uvedeným linkem vaši emailovou adresu")
            ->action(
                "Ověřit emailovou adresu",
                $this->verificationUrl($notifiable)
            )
            ->line(Lang::getFromJson('If you did not create an account, no further action is required.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // create token
        $token = app(PasswordBroker::class)->createToken(User::findOrFail($notifiable->getKey()));

        // prepare signed link
        $link = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), [
                'id' => $notifiable->getKey(),
                'token' => $token
            ]
        );

        // prepare remote link
        $remoteLink = config('frontend.url') . 'vet/verify?link=' . base64_encode($link);

        return $remoteLink;
    }
}