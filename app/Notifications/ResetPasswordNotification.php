<?php

namespace App\Notifications;

use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword as FilamentResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends FilamentResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->url ?? (
            Filament::hasPlugin('admin') || Filament::getCurrentPanel() || Filament::getPanels()
                ? Filament::getPanel('admin')->getResetPasswordUrl($this->token, $notifiable)
                : url(route('filament.admin.auth.password-reset.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false))
        );

        return (new MailMessage)
            ->subject('Redefinição de Senha - Vitória Hospitalar')
            ->greeting('Olá, ' . ($notifiable->name ?? 'Usuário') . '!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta no Portal da Vitória Hospitalar.')
            ->action('Redefinir Minha Senha', $resetUrl)
            ->line('Este link de redefinição de senha expirará em ' . config('auth.passwords.users.expire', 60) . ' minutos.')
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária e sua conta permanece protegida.')
            ->salutation('Atenciosamente,' . "\n" . 'Equipe Vitória Hospitalar');
    }
}
