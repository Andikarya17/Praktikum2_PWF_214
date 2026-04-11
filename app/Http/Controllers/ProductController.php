<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);

        return view('product.index', compact('products'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        // Map quantity -> qty untuk database column
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        $product = Product::create($validated);

        return redirect()->route('product.index')->with('success', 'Product created successfully.');
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        $users = User::orderBy('name')->get();

        return view('product.create', compact('users'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.view', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Policy: hanya admin pemilik product yang bisa update
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        // Map quantity -> qty untuk database column
        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    public function edit(Product $product)
    {
        // Policy: hanya admin pemilik product yang bisa edit
        Gate::authorize('update', $product);

        $users = User::orderBy('name')->get();

        return view('product.edit', compact('product', 'users'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // Policy: hanya admin pemilik product yang bisa delete
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus');
    }
}