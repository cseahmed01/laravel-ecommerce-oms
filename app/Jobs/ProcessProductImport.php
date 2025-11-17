<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImport;
use App\Models\ProductImportLog;
use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ProductImport $import
    ) {}

    public function handle(): void
    {
        $this->import->update(['status' => 'processing']);

        $filePath = Storage::disk('public')->path($this->import->file_path);

        if (!file_exists($filePath)) {
            $this->import->update(['status' => 'failed']);
            return;
        }

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file); // Skip header row
        $rowNumber = 1;
        $processed = 0;

        while (($row = fgetcsv($file)) !== false) {
            $rowNumber++;
            $data = array_combine($headers, $row);

            try {
                $this->processRow($data);
                ProductImportLog::create([
                    'import_id' => $this->import->id,
                    'row_number' => $rowNumber,
                    'status' => 'success',
                ]);
                $processed++;
            } catch (\Exception $e) {
                ProductImportLog::create([
                    'import_id' => $this->import->id,
                    'row_number' => $rowNumber,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ]);
            }
        }

        fclose($file);

        $this->import->update([
            'status' => 'completed',
            'total_rows' => $rowNumber - 1, // Subtract header
            'processed_rows' => $processed,
        ]);
    }

    private function processRow(array $data): void
    {
        // Expected CSV columns: name, description, sku, price, stock, attributes (JSON string)
        $productData = [
            'vendor_id' => $this->import->vendor_id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'status' => 'active',
        ];

        $product = Product::create($productData);

        $variantData = [
            'product_id' => $product->id,
            'sku' => $data['sku'],
            'price' => (float) $data['price'],
            'stock' => (int) $data['stock'],
            'attributes' => json_decode($data['attributes'] ?? '{}', true),
        ];

        ProductVariant::create($variantData);
    }
}