<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('variants');

        // Filter by vendor for non-admin users
        if (!auth('api')->user()->hasRole('admin')) {
            $query->where('vendor_id', auth('api')->id());
        }

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['vendor_id'] = auth('api')->id();

        $product = Product::create($data);

        // Create variants
        foreach ($data['variants'] as $variantData) {
            $variantData['product_id'] = $product->id;
            ProductVariant::create($variantData);
        }

        return response()->json(new ProductResource($product->load('variants')), 201);
    }

    public function show(Product $product): JsonResponse
    {
        // Check authorization
        if (!auth()->user()->hasRole('admin') && $product->vendor_id !== auth()->id()) {
            abort(403);
        }

        return response()->json(new ProductResource($product->load('variants')));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json(new ProductResource($product->load('variants')));
    }

    public function destroy(Product $product): JsonResponse
    {
        // Check authorization
        if (!auth('api')->user()->hasRole('admin') && $product->vendor_id !== auth('api')->id()) {
            abort(403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function addVariant(Request $request, Product $product): JsonResponse
    {
        // Check authorization
        if (!auth('api')->user()->hasRole('admin') && $product->vendor_id !== auth('api')->id()) {
            abort(403);
        }

        $request->validate([
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            ...$request->only(['sku', 'price', 'stock', 'attributes'])
        ]);

        return response()->json(new ProductVariantResource($variant), 201);
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        // Check authorization
        if (!auth('api')->user()->hasRole('admin') && $product->vendor_id !== auth('api')->id()) {
            abort(403);
        }

        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $request->validate([
            'sku' => 'sometimes|required|string|unique:product_variants,sku,' . $variant->id,
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'attributes' => 'nullable|array',
        ]);

        $variant->update($request->only(['sku', 'price', 'stock', 'attributes']));

        return response()->json(new ProductVariantResource($variant));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('imports/products', 'public');

        $import = \App\Models\ProductImport::create([
            'vendor_id' => auth()->id(),
            'file_path' => $path,
            'total_rows' => 0, // Will be updated by the job
        ]);

        // Dispatch import job
        \App\Jobs\ProcessProductImport::dispatch($import);

        return response()->json([
            'message' => 'Import started',
            'import_id' => $import->id,
        ], 202);
    }

    public function importStatus(\App\Models\ProductImport $import): JsonResponse
    {
        // Check authorization
        if (!auth()->user()->hasRole('admin') && $import->vendor_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'created_at' => $import->created_at,
            'updated_at' => $import->updated_at,
        ]);
    }

    public function deleteVariant(Product $product, ProductVariant $variant): JsonResponse
    {
        // Check authorization
        if (!auth()->user()->hasRole('admin') && $product->vendor_id !== auth()->id()) {
            abort(403);
        }

        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->delete();

        return response()->json(['message' => 'Variant deleted successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $products = Product::search($query)->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}