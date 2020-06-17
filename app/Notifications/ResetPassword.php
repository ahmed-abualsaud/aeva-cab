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
            ->subject('Reset your password')
            ->greeting('Hello,')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Your Password', url(config('app.url').route('password.reset', [$this->type, $this->token, urlencode($notifiable->email)], false)))
            ->line('If you did not request a password reset, no further action is required.');
    }
}