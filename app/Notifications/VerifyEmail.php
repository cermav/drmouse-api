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

        /*
        $email = $user->email;
        $data = [
            'doctor' => $doctor,
            'user' => $user
        ];
        Mail::send('emails/registration', $data, function ($message) use ($email) {
            $message->to($email)
                ->subject('Dr.Mouse ověření emailu');
        });
        */

        return (new MailMessage())
            ->subject(Lang::getFromJson('Oveření registračního emailu'))
            ->greeting("Dobrý den,")
            ->line("prosím, potvrďte níže uvedeným linkem vaši emailovou adresu")
            ->action(
                "Ověřit emailovou adresu",
                $this->verificationUrl($notifiable)
            )
            ->line("Pokud jste nežádali o registraci v databazi veterinářů Dr. Mouse, pak tento email prosím ignorujte.");
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $user = User::findOrFail($notifiable->getKey());

        // create token
        $token = app(PasswordBroker::class)->createToken($user);

        // prepare signed link
        $link = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), [
                'id' => $notifiable->getKey(),
                'token' => $token
            ]
        );

        // prepare remote link
        if ($user->role_id == 3) {
            $remoteLink = config('frontend.url') . 'my/verify?link=' . base64_encode($link);
        } else {
            $remoteLink = config('frontend.url') . 'vet/verify?link=' . base64_encode($link);
        }

//        $remoteLink = config('frontend.url') . 'vet/verify?id=' . $notifiable->getKey() . parse_url($link, PHP_URL_QUERY );

        return $remoteLink;
    }
}
