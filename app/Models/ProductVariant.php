<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'attributes' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= config('inventory.low_stock_threshold', 10);
    }

    public function updateStock(int $quantity, string $type, ?string $referenceType = null, ?string $referenceId = null): void
    {
        if ($type === 'deduct') {
            $this->decrement('stock', $quantity);
        } elseif ($type === 'add') {
            $this->increment('stock', $quantity);
        }

        // Log the inventory change
        InventoryLog::create([
            'product_variant_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);

        // Check for low stock
        if ($this->isLowStock() && $type === 'deduct') {
            // Dispatch low stock alert job
            dispatch(new \App\Jobs\LowStockAlert($this));
        }
    }
}