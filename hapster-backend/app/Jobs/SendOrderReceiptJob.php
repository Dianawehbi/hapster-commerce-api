<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderReceiptJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 5;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        // Idempotency check
        if ($this->order->receipt_sent) {
            return;
        }

        // Simulate sending receipt (e.g., log message)
        Log::info("Receipt sent for Order #{$this->order->id}");

        $this->order->receipt_sent = true;
        $this->order->save();
    }
}
