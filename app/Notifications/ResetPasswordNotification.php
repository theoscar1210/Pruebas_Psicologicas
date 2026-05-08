<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Recuperación de contraseña — MenteClara')
            ->greeting('Hola,')
            ->line('Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en MenteClara.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
            ->line('Si no solicitaste el restablecimiento, no es necesario que hagas nada.')
            ->salutation('MenteClara · Sistema de Evaluación Psicológica');
    }
}
