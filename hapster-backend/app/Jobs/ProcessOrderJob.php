<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;
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
        $order = $this->order->fresh('items.product');
        if ($order->status != 'pending')
            return;

        $total = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product->stock < $item->qty) {
                $order->status = 'failed';
                $order->save();
                return;
            }
            $product->decrement('stock', $item->qty);
            $total += $item->qty * $item->price;
        }

        $order->total_price = $total;
        $order->status = 'completed';
        $order->save();
    }
}


