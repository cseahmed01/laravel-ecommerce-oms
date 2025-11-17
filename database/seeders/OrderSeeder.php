<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::role('customer')->get();

        if ($customers->isEmpty()) {
            // Create some customers if none exist
            $customers = collect([
                User::factory()->create([
                    'name' => 'John Customer',
                    'email' => 'john@example.com',
                ])->assignRole('customer'),
                User::factory()->create([
                    'name' => 'Jane Customer',
                    'email' => 'jane@example.com',
                ])->assignRole('customer'),
                User::factory()->create([
                    'name' => 'Bob Customer',
                    'email' => 'bob@example.com',
                ])->assignRole('customer'),
            ]);
        }

        // Get admin user for status changes
        $admin = User::role('admin')->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'System Admin',
                'email' => 'admin@system.com',
            ])->assignRole('admin');
        }

        $productVariants = ProductVariant::with('product')->get();

        if ($productVariants->isEmpty()) {
            $this->command->warn('No product variants found. Please run ProductSeeder first.');
            return;
        }

        $orders = [
            [
                'customer' => $customers->first(),
                'status' => 'delivered',
                'total_amount' => 0, // Will be calculated
                'shipping_address' => '123 Main St, Anytown, USA 12345',
                'billing_address' => '123 Main St, Anytown, USA 12345',
                'items' => [
                    ['variant' => $productVariants->where('sku', 'WBH-BLK-S')->first() ?? $productVariants->random(), 'quantity' => 1],
                    ['variant' => $productVariants->where('sku', 'CTS-BLK-M')->first() ?? $productVariants->random(), 'quantity' => 2],
                ],
                'status_history' => ['pending', 'processing', 'shipped', 'delivered'],
            ],
            [
                'customer' => $customers->skip(1)->first(),
                'status' => 'shipped',
                'total_amount' => 0,
                'shipping_address' => '456 Oak Ave, Somewhere, USA 67890',
                'billing_address' => '456 Oak Ave, Somewhere, USA 67890',
                'items' => [
                    ['variant' => $productVariants->where('sku', 'SP128-BLK')->first() ?? $productVariants->random(), 'quantity' => 1],
                    ['variant' => $productVariants->where('sku', 'GM-RGB-BLK')->first() ?? $productVariants->random(), 'quantity' => 1],
                ],
                'status_history' => ['pending', 'processing', 'shipped'],
            ],
            [
                'customer' => $customers->skip(2)->first(),
                'status' => 'processing',
                'total_amount' => 0,
                'shipping_address' => '789 Pine Rd, Elsewhere, USA 54321',
                'billing_address' => '789 Pine Rd, Elsewhere, USA 54321',
                'items' => [
                    ['variant' => $productVariants->where('sku', 'DJ-BLU-32')->first() ?? $productVariants->random(), 'quantity' => 1],
                    ['variant' => $productVariants->where('sku', 'CCM-WHT-12')->first() ?? $productVariants->random(), 'quantity' => 3],
                    ['variant' => $productVariants->where('sku', 'TPS-GRY-18')->first() ?? $productVariants->random(), 'quantity' => 1],
                ],
                'status_history' => ['pending', 'processing'],
            ],
            [
                'customer' => $customers->first(),
                'status' => 'pending',
                'total_amount' => 0,
                'shipping_address' => '321 Elm St, Nowhere, USA 98765',
                'billing_address' => '321 Elm St, Nowhere, USA 98765',
                'items' => [
                    ['variant' => $productVariants->where('sku', 'SSWB-SIL-32')->first() ?? $productVariants->random(), 'quantity' => 1],
                ],
                'status_history' => ['pending'],
            ],
            [
                'customer' => $customers->skip(1)->first(),
                'status' => 'cancelled',
                'total_amount' => 0,
                'shipping_address' => '654 Maple Dr, Anywhere, USA 13579',
                'billing_address' => '654 Maple Dr, Anywhere, USA 13579',
                'items' => [
                    ['variant' => $productVariants->where('sku', 'LES-WHT-10')->first() ?? $productVariants->random(), 'quantity' => 1],
                ],
                'status_history' => ['pending', 'cancelled'],
            ],
        ];

        foreach ($orders as $orderData) {
            $customer = $orderData['customer'];

            // Skip if customer is null
            if (!$customer) {
                continue;
            }

            $status = $orderData['status'];
            $items = $orderData['items'];
            $statusHistory = $orderData['status_history'];

            unset($orderData['customer'], $orderData['status'], $orderData['items'], $orderData['status_history']);

            $orderData['customer_id'] = $customer->id;
            $orderData['order_number'] = 'ORD-' . strtoupper(uniqid());

            // Calculate total amount
            $totalAmount = 0;
            foreach ($items as $item) {
                $variant = $item['variant'];
                if ($variant) {
                    $totalAmount += $variant->price * $item['quantity'];
                }
            }
            $orderData['total_amount'] = $totalAmount;

            $order = Order::create($orderData);

            // Create order items
            foreach ($items as $item) {
                $variant = $item['variant'];
                if ($variant) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $variant->price,
                        'subtotal' => $variant->price * $item['quantity'],
                    ]);

                    // Update inventory if order is not cancelled
                    if ($status !== 'cancelled') {
                        $variant->updateStock($item['quantity'], 'deduct', 'order', $order->id);
                    }
                }
            }

            // Create status logs
            $createdAt = $order->created_at;
            $previousStatus = null;
            foreach ($statusHistory as $index => $statusValue) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'old_status' => $previousStatus,
                    'new_status' => $statusValue,
                    'changed_by' => $admin->id,
                    'created_at' => $createdAt->copy()->addHours($index * 24), // Simulate time progression
                    'updated_at' => $createdAt->copy()->addHours($index * 24),
                ]);
                $previousStatus = $statusValue;
            }
        }
    }
}