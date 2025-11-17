<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = User::role('vendor')->get();

        if ($vendors->isEmpty()) {
            // Create some vendors if none exist
            $vendor1 = User::factory()->create([
                'name' => 'TechStore Vendor',
                'email' => 'techstore@example.com',
            ]);
            $vendor1->assignRole('vendor');

            $vendor2 = User::factory()->create([
                'name' => 'FashionHub Vendor',
                'email' => 'fashionhub@example.com',
            ]);
            $vendor2->assignRole('vendor');

            $vendor3 = User::factory()->create([
                'name' => 'HomeGoods Vendor',
                'email' => 'homegoods@example.com',
            ]);
            $vendor3->assignRole('vendor');

            $vendors = collect([$vendor1, $vendor2, $vendor3]);
        }

        $products = [
            // Electronics
            [
                'vendor' => $vendors->first(),
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation, 30-hour battery life, and premium sound quality.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'WBH-BLK-S', 'price' => 89.99, 'stock' => 45, 'attributes' => ['color' => 'black', 'size' => 'standard']],
                    ['sku' => 'WBH-WHT-S', 'price' => 89.99, 'stock' => 32, 'attributes' => ['color' => 'white', 'size' => 'standard']],
                    ['sku' => 'WBH-BLU-S', 'price' => 89.99, 'stock' => 28, 'attributes' => ['color' => 'blue', 'size' => 'standard']],
                ]
            ],
            [
                'vendor' => $vendors->first(),
                'name' => 'Smartphone 128GB',
                'description' => 'Latest smartphone with 128GB storage, 48MP camera, and fast charging capability.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'SP128-BLK', 'price' => 699.99, 'stock' => 15, 'attributes' => ['color' => 'black', 'storage' => '128GB']],
                    ['sku' => 'SP128-WHT', 'price' => 699.99, 'stock' => 12, 'attributes' => ['color' => 'white', 'storage' => '128GB']],
                    ['sku' => 'SP128-BLU', 'price' => 699.99, 'stock' => 8, 'attributes' => ['color' => 'blue', 'storage' => '128GB']],
                ]
            ],
            [
                'vendor' => $vendors->first(),
                'name' => 'Gaming Mouse RGB',
                'description' => 'Professional gaming mouse with RGB lighting, 16000 DPI sensor, and programmable buttons.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'GM-RGB-BLK', 'price' => 59.99, 'stock' => 67, 'attributes' => ['color' => 'black', 'connectivity' => 'wired']],
                    ['sku' => 'GM-RGB-WHT', 'price' => 59.99, 'stock' => 43, 'attributes' => ['color' => 'white', 'connectivity' => 'wired']],
                ]
            ],

            // Fashion
            [
                'vendor' => $vendors->skip(1)->first(),
                'name' => 'Cotton T-Shirt',
                'description' => 'Comfortable 100% cotton t-shirt, perfect for casual wear. Available in multiple sizes and colors.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'CTS-BLK-S', 'price' => 19.99, 'stock' => 120, 'attributes' => ['color' => 'black', 'size' => 'S']],
                    ['sku' => 'CTS-BLK-M', 'price' => 19.99, 'stock' => 95, 'attributes' => ['color' => 'black', 'size' => 'M']],
                    ['sku' => 'CTS-BLK-L', 'price' => 19.99, 'stock' => 78, 'attributes' => ['color' => 'black', 'size' => 'L']],
                    ['sku' => 'CTS-WHT-S', 'price' => 19.99, 'stock' => 110, 'attributes' => ['color' => 'white', 'size' => 'S']],
                    ['sku' => 'CTS-WHT-M', 'price' => 19.99, 'stock' => 88, 'attributes' => ['color' => 'white', 'size' => 'M']],
                    ['sku' => 'CTS-WHT-L', 'price' => 19.99, 'stock' => 65, 'attributes' => ['color' => 'white', 'size' => 'L']],
                ]
            ],
            [
                'vendor' => $vendors->skip(1)->first(),
                'name' => 'Designer Jeans',
                'description' => 'Premium quality designer jeans with perfect fit and durability. Made from high-quality denim.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'DJ-BLU-30', 'price' => 89.99, 'stock' => 25, 'attributes' => ['color' => 'blue', 'waist' => '30']],
                    ['sku' => 'DJ-BLU-32', 'price' => 89.99, 'stock' => 30, 'attributes' => ['color' => 'blue', 'waist' => '32']],
                    ['sku' => 'DJ-BLU-34', 'price' => 89.99, 'stock' => 22, 'attributes' => ['color' => 'blue', 'waist' => '34']],
                    ['sku' => 'DJ-BLK-30', 'price' => 89.99, 'stock' => 18, 'attributes' => ['color' => 'black', 'waist' => '30']],
                    ['sku' => 'DJ-BLK-32', 'price' => 89.99, 'stock' => 20, 'attributes' => ['color' => 'black', 'waist' => '32']],
                ]
            ],

            // Home Goods
            [
                'vendor' => $vendors->skip(2)->first(),
                'name' => 'Ceramic Coffee Mug',
                'description' => 'Beautiful ceramic coffee mug with comfortable handle. Perfect for your morning coffee.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'CCM-WHT-12', 'price' => 12.99, 'stock' => 150, 'attributes' => ['color' => 'white', 'capacity' => '12oz']],
                    ['sku' => 'CCM-BLK-12', 'price' => 12.99, 'stock' => 135, 'attributes' => ['color' => 'black', 'capacity' => '12oz']],
                    ['sku' => 'CCM-BLU-12', 'price' => 12.99, 'stock' => 98, 'attributes' => ['color' => 'blue', 'capacity' => '12oz']],
                ]
            ],
            [
                'vendor' => $vendors->skip(2)->first(),
                'name' => 'Throw Pillow Set',
                'description' => 'Set of 2 decorative throw pillows. Made from high-quality fabric with soft filling.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'TPS-GRY-18', 'price' => 34.99, 'stock' => 45, 'attributes' => ['color' => 'gray', 'size' => '18x18']],
                    ['sku' => 'TPS-BLU-18', 'price' => 34.99, 'stock' => 38, 'attributes' => ['color' => 'blue', 'size' => '18x18']],
                    ['sku' => 'TPS-CRM-18', 'price' => 34.99, 'stock' => 52, 'attributes' => ['color' => 'cream', 'size' => '18x18']],
                ]
            ],
            [
                'vendor' => $vendors->skip(2)->first(),
                'name' => 'Stainless Steel Water Bottle',
                'description' => 'Insulated stainless steel water bottle that keeps drinks cold for 24 hours or hot for 12 hours.',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'SSWB-SIL-32', 'price' => 29.99, 'stock' => 75, 'attributes' => ['color' => 'silver', 'capacity' => '32oz']],
                    ['sku' => 'SSWB-BLK-32', 'price' => 29.99, 'stock' => 68, 'attributes' => ['color' => 'black', 'capacity' => '32oz']],
                    ['sku' => 'SSWB-BLU-32', 'price' => 29.99, 'stock' => 42, 'attributes' => ['color' => 'blue', 'capacity' => '32oz']],
                ]
            ],

            // Low stock items for testing alerts
            [
                'vendor' => $vendors->first(),
                'name' => 'Limited Edition Sneakers',
                'description' => 'Exclusive limited edition sneakers. Only a few pairs left!',
                'status' => 'active',
                'variants' => [
                    ['sku' => 'LES-WHT-9', 'price' => 199.99, 'stock' => 3, 'attributes' => ['color' => 'white', 'size' => '9']],
                    ['sku' => 'LES-WHT-10', 'price' => 199.99, 'stock' => 2, 'attributes' => ['color' => 'white', 'size' => '10']],
                    ['sku' => 'LES-WHT-11', 'price' => 199.99, 'stock' => 1, 'attributes' => ['color' => 'white', 'size' => '11']],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $vendor = $productData['vendor'];

            // Skip if vendor is null
            if (!$vendor) {
                continue;
            }

            unset($productData['vendor']);

            $variants = $productData['variants'];
            unset($productData['variants']);

            $slug = \Illuminate\Support\Str::slug($productData['name']);

            // Check if product already exists
            if (Product::where('slug', $slug)->exists()) {
                continue;
            }

            $productData['vendor_id'] = $vendor->id;
            $productData['slug'] = $slug;

            $product = Product::create($productData);

            foreach ($variants as $variantData) {
                // Check if variant SKU already exists
                if (!ProductVariant::where('sku', $variantData['sku'])->exists()) {
                    $variantData['product_id'] = $product->id;
                    ProductVariant::create($variantData);
                }
            }
        }
    }
}