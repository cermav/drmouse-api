<?php

namespace App\Notifications;

use App\Models\NewsletterUser;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyNewsletterEmail extends VerifyEmailBase
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
            ->subject('Oveření registrace k newsletteru')
            ->view('emails.verify-newsletter', ['verify_link' => $this->verificationUrl($notifiable)]);
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
        $token = app(PasswordBroker::class)->createToken(NewsletterUser::findOrFail($notifiable->getKey()));

        // prepare signed link
        $link = URL::temporarySignedRoute(
            'newsletter.verify', Carbon::now()->addMinutes(60), [
                'id' => $notifiable->getKey(),
                'token' => $token
            ]
        );

        // prepare remote link
        $remoteLink = config('frontend.url') . 'newsletter/verify?link=' . base64_encode($link);

        return $remoteLink;
    }
}