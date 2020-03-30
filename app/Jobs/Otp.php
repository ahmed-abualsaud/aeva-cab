<?php

namespace App\Jobs;

use App\Helpers\Otp as Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Otp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $to;
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Sms::send($this->to, $this->message);
    }
}
