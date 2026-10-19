<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $apiUrl = $this->verificationUrl($notifiable);
        $frontendUrl = config('app.frontend_url').'/verify-email?url='.urlencode($apiUrl);

        return (new MailMessage)
            ->subject('Confirma adresa de email - metCasa')
            ->greeting('Buna, '.$notifiable->name.'!')
            ->line('Multumim ca te-ai inregistrat pe metCasa. Apasa butonul de mai jos pentru a-ti confirma adresa de email.')
            ->action('Confirma emailul', $frontendUrl)
            ->line('Link-ul expira in 60 de minute.')
            ->line('Daca nu ai creat tu acest cont, ignora acest email.');
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'v1.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
