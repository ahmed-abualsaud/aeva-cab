<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\FirebasePushNotification as Firebase;

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $devices;
    private $message;
    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devices, $message, $data = false)
    {
        $this->devices = $devices;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Firebase::push($this->devices, 'Qruz Business', $this->message, $this->data);
    }
}
