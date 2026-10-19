<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = config('app.frontend_url').'/reset-password?token='.$this->token
            .'&email='.urlencode($notifiable->getEmailForPasswordReset());

        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Resetare parola - metCasa')
            ->greeting('Buna, '.$notifiable->name.'!')
            ->line('Ai cerut resetarea parolei pentru contul metCasa. Apasa butonul de mai jos pentru a alege o parola noua.')
            ->action('Reseteaza parola', $url)
            ->line('Link-ul expira in '.$expire.' minute.')
            ->line('Daca nu ai cerut tu resetarea, ignora acest email - parola ta ramane neschimbata.');
    }
}
