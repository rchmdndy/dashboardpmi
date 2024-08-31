<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        $email = $notifiable->getEmailForVerification();
        $key = config('app.key');
        $hash = hash_hmac('sha256', $email.$key, $key);

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['email' => $notifiable->getEmailForVerification(),
                'hash' => $hash]
        );
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda')
            ->line('Kami melihat Anda memulai proses pendaftaran di platform kami, namun kami belum menerima verifikasi akun Anda. Untuk mengaktifkan akun Anda sepenuhnya dan menyelami pengalaman Platform kami, silakan klik tombol di bawah untuk memverifikasi alamat email Anda:')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Jika Anda tidak membuat akun, tidak ada tindakan lebih lanjut yang diperlukan.');
    }
}
