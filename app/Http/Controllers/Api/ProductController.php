<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        try {
            $products = Product::with(['user', 'category'])->get();

            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data produk',
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['user_id'] = Auth::id();

            // Map quantity -> qty untuk database column
            if (isset($validated['quantity'])) {
                $validated['qty'] = $validated['quantity'];
                unset($validated['quantity']);
            }

            $product = Product::create($validated);

            Log::info('Menambah data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah product', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menambah produk',
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product)
            {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data produk',
            ], 500);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'quantity' => 'sometimes|required|integer',
                'qty' => 'sometimes|required|integer',
                'price' => 'sometimes|required|numeric',
                'category_id' => 'nullable|exists:category,id',
            ]);

            // Map quantity -> qty untuk database column
            if (isset($validated['quantity'])) {
                $validated['qty'] = $validated['quantity'];
                unset($validated['quantity']);
            }

            $product->update($validated);

            Log::info('Mengupdate data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Product berhasil diupdate!!',
                'data' => $product,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat mengupdate product', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate produk',
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $product->delete();

            Log::info('Menghapus data produk', [
                'id' => $id
            ]);

            return response()->json([
                'message' => 'Product berhasil dihapus!!',
            ], 204);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus product', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus produk',
            ], 500);
        }
    }
}
