<?php

namespace App\Jobs;

use App\Helpers\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtp implements ShouldQueue
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
        $this->queue = 'dev-high';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('Processing.a.podcast');

        //Otp::send($this->to, $this->message);
    }
}
