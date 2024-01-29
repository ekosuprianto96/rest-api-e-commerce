<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\SettingWebsite;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LinkResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $data;
    public $settings;
    public function __construct($data)
    {
        $this->data = $data;
        $this->settings = SettingWebsite::first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hallo '.$this->data['nama'].'')
                    ->line('Kami mendeteksi bahwa Anda meminta reset password untuk akun Anda.')
                    ->line('Silakan klik link di bawah ini untuk mengatur ulang kata sandi Anda:')
                    ->action('Reset Password', $this->data['url'])
                    ->line('Jika Anda tidak merasa melakukan permintaan ini, mohon abaikan pesan ini. ')
                    ->line('Keamanan akun Anda sangat penting bagi kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
