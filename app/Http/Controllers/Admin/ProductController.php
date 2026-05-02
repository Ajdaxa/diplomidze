<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->paginate(24);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Product::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $product = $this->persistProduct($request, new Product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар «'.$product->name.'» создан.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Product::CATEGORIES,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->persistProduct($request, $product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар обновлен.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар удален.');
    }

    private function persistProduct(Request $request, Product $product): Product
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Product::CATEGORIES))],
            'image' => ['nullable', 'url'],
            'secondary_image' => ['nullable', 'url'],
            'color' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:20'],
            'available_sizes' => ['nullable', 'string', 'max:500'],
            'display_colors' => ['nullable', 'string', 'max:500'],
            'is_new_collection' => ['nullable', 'boolean'],
            'is_limited_edition' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $sizes = $this->parseList($validated['available_sizes'] ?? '');
        $colors = $this->parseList($validated['display_colors'] ?? '');

        $slugBase = Str::slug($validated['name']);

        $product->fill([
            'name' => $validated['name'],
            'slug' => $product->exists ? ($slugBase.'-'.$product->id) : ($slugBase.'-'.Str::lower(Str::random(5))),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category' => $validated['category'],
            'image' => $validated['image'] ?? null,
            'secondary_image' => $validated['secondary_image'] ?? null,
            'color' => $validated['color'] ?? null,
            'size' => $validated['size'] ?? null,
            'available_sizes' => $sizes === [] ? null : $sizes,
            'display_colors' => $colors === [] ? null : $colors,
            'is_new_collection' => $request->boolean('is_new_collection'),
            'is_limited_edition' => $request->boolean('is_limited_edition'),
            'is_active' => $request->boolean('is_active', true),
        ]);
        $product->save();
        $product->update(['slug' => $slugBase.'-'.$product->id]);

        return $product->fresh();
    }

    /** @return list<string> */
    private function parseList(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[,\s]+/', $raw) ?: [])));
    }
}
