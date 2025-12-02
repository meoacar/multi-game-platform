<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Yeni notification instance oluştur
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Notification'ın gönderileceği kanallar
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mail mesajı
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Şifre Sıfırlama Talebi')
            ->greeting('Merhaba!')
            ->line('Hesabınız için şifre sıfırlama talebi aldık.')
            ->action('Şifremi Sıfırla', $url)
            ->line('Bu link 60 dakika içinde geçerliliğini yitirecektir.')
            ->line('Eğer şifre sıfırlama talebinde bulunmadıysanız, bu e-postayı görmezden gelebilirsiniz.')
            ->salutation('Saygılarımızla, PUBG Topluluk Ekibi');
    }

    /**
     * Array representation
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
