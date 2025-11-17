<?php

namespace App\Jobs;

use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LowStockAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ProductVariant $variant
    ) {}

    public function handle(): void
    {
        // Log the alert
        Log::warning("Low stock alert for product variant {$this->variant->sku}: {$this->variant->stock} remaining");

        // Send email notification to vendor
        // For now, just log it. In a real app, you'd send an email
        // Mail::to($this->variant->product->vendor->email)->send(new LowStockAlertMail($this->variant));
    }
}