<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $message;
    public $title;
    public $url;
    public $view;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $title = "Qruz", $url = null, $view = 'emails.default')
    {
        $this->message = $message;
        $this->title = $title;
        $this->url = $url;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() 
    {
        return $this->markdown($this->view)->subject($this->title);
    }
}
