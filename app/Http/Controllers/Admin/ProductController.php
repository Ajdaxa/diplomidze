<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'url'],
            'secondary_image' => ['nullable', 'url'],
            'color' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:20'],
            'is_new_collection' => ['nullable', 'boolean'],
            'is_limited_edition' => ['nullable', 'boolean'],
        ]);

        Product::query()->create([
            ...$validated,
            'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(5)),
            'is_new_collection' => $request->boolean('is_new_collection'),
            'is_limited_edition' => $request->boolean('is_limited_edition'),
            'is_active' => true,
        ]);

        return back()->with('status', 'Товар создан.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'url'],
            'secondary_image' => ['nullable', 'url'],
            'color' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:20'],
            'is_new_collection' => ['nullable', 'boolean'],
            'is_limited_edition' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update([
            ...$validated,
            'slug' => Str::slug($validated['name']).'-'.$product->id,
            'is_new_collection' => $request->boolean('is_new_collection'),
            'is_limited_edition' => $request->boolean('is_limited_edition'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Товар обновлен.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('status', 'Товар удален.');
    }
}
