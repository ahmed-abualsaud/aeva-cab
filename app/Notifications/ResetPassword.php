<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends ResetPasswordNotification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $type;
 
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $type)
    {
        $this->token = $token;
        $this->type = $type;
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
            ->subject('Qruz password reset link')
            ->greeting('Hello, here\'s how to reset your password.')
            ->line('We have received a request to have your password reset.')
            ->action('Reset Your Password', url(config('app.url').route('password.reset', [$this->type, $this->token, urlencode($notifiable->email)], false)))
            ->line('If you did not make this request, please ignore this email.');
    }
}