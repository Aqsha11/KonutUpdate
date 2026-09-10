<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = setting('site_name', 'Konut.Update');

        return (new MailMessage)
            ->subject("Verifikasi Email - {$siteName}")
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Terima kasih telah mendaftar. Silakan verifikasi alamat email Anda untuk mengaktifkan akun.')
            ->action('Verifikasi Email', $this->verificationUrl($notifiable))
            ->line('Jika Anda tidak merasa mendaftar di '.$siteName.', abaikan email ini.');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]);
    }
}
