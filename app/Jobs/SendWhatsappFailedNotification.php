<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\WhatsappNotificationService;

class SendWhatsappFailedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $phone, private $transactionLink, private $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WhatsappNotificationService::sendFailedMessage($this->phone, $this->transactionLink, $this->data);
    }
}
