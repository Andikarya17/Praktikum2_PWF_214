<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-category');

        $categories = Category::withCount('products')->get();

        return view('category.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('manage-category');

        return view('category.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-category');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:category,name',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            Category::create($validated);

            return redirect()
                ->route('category.index')
                ->with('success', 'Category created successfully.');
        } catch (QueryException $e) {
            Log::error('Category store database error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error while creating category.');
        } catch (\Throwable $e) {
            Log::error('Category store unexpected error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred.');
        }
    }

    public function edit(Category $category)
    {
        Gate::authorize('manage-category');

        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('manage-category');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:category,name,' . $category->id,
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            $category->update($validated);

            return redirect()
                ->route('category.index')
                ->with('success', 'Category updated successfully.');
        } catch (QueryException $e) {
            Log::error('Category update database error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database error while updating category.');
        } catch (\Throwable $e) {
            Log::error('Category update unexpected error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred.');
        }
    }

    public function destroy(Category $category)
    {
        Gate::authorize('manage-category');

        $category->delete();

        return redirect()->route('category.index')->with('success', 'Category berhasil dihapus');
    }
}
