<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['user', 'category'])->paginate(10);

        return view('product.index', compact('products'));
    }

    public function store(StoreProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validated();

        // Map quantity -> qty untuk database column
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        try {
            Product::create($validated);

            return redirect()
                ->route('product.index')
                ->with('success', 'Product created successfully.');
        } catch (QueryException $e) {
            Log::error('Product store database error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error while creating product.');
        } catch (\Throwable $e) {
            Log::error('Product store unexpected error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred.');
        }
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('product.create', compact('users', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('product.view', compact('product'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        // Policy: hanya admin pemilik product yang bisa update
        Gate::authorize('update', $product);

        $validated = $request->validated();

        // Map quantity -> qty untuk database column
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        try {
            $product->update($validated);

            return redirect()
                ->route('product.index')
                ->with('success', 'Product updated successfully.');
        } catch (QueryException $e) {
            Log::error('Product update database error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error while updating product.');
        } catch (\Throwable $e) {
            Log::error('Product update unexpected error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred.');
        }
    }

    public function edit(Product $product)
    {
        // Policy: hanya admin pemilik product yang bisa edit
        Gate::authorize('update', $product);

        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('product.edit', compact('product', 'users', 'categories'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // Policy: hanya admin pemilik product yang bisa delete
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus');
    }

    public function export()
    {
        Gate::authorize('manage-product');

        $products = Product::with('user')->get();

        $csvHeader = ['ID', 'Name', 'Qty', 'Price', 'Owner'];
        $csvRows = $products->map(function ($product) {
            return [
                $product->id,
                $product->name,
                $product->qty,
                $product->price,
                $product->user->name ?? '-',
            ];
        });

        $callback = function () use ($csvHeader, $csvRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($csvRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'products_export.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}