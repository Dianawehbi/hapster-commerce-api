<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $order;

    public $tries = 3;
    public $backoff = 5;


    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Avoid double-processing
        if ($this->order->status !== 'pending') {
            return;
        }

        $this->order->update(['status' => 'processing']);

        $totalPrice = 0;
        $failed = false;

        foreach ($this->order->items as $item) {
            $product = $item->product;

            if ($product->stock < $item->qty) {
                $failed = true;
                break;
            }

            // Decrease stock
            $product->decrement('stock', $item->qty);

            // Calculate total price
            $totalPrice += $item->qty * $item->price;
        }

        // Update order status and total_price
        $this->order->update([
            'status' => $failed ? 'failed' : 'completed',
            'total_price' => $totalPrice
        ]);

        // Dispatch receipt job only if completed
        if (!$failed) {
            SendOrderReceiptJob::dispatch($this->order);
        }
    }
}


