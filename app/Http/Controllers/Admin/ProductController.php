<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function categoriesCollection()
    {
        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->get();
        if ($categories->isNotEmpty()) {
            return $categories;
        }

        $sort = 0;
        foreach (Product::CATEGORIES as $slug => $name) {
            Category::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'sort_order' => $sort++, 'is_active' => true]
            );
        }

        return Category::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function index()
    {
        $products = Product::query()->latest()->paginate(24);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->categoriesCollection();

        return view('admin.products.create', compact('categories'));
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
        $categories = $this->categoriesCollection();

        return view('admin.products.edit', compact('product', 'categories'));
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
            'composition' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'url'],
            'secondary_image' => ['nullable', 'url'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'secondary_image_file' => ['nullable', 'image', 'max:5120'],
            'color' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female,unisex'],
            'size' => ['nullable', 'string', 'max:20'],
            'available_sizes' => ['nullable', 'string', 'max:500'],
            'is_new_collection' => ['nullable', 'boolean'],
            'is_limited_edition' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $sizes = $this->parseList($validated['available_sizes'] ?? '');
        $imageUrl = $validated['image'] ?? $product->image;
        $secondaryImageUrl = $validated['secondary_image'] ?? $product->secondary_image;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        if ($request->hasFile('secondary_image_file')) {
            $path = $request->file('secondary_image_file')->store('products', 'public');
            $secondaryImageUrl = asset('storage/'.$path);
        }

        $slugBase = Str::slug($validated['name']);
        $category = Category::query()->findOrFail((int) $validated['category_id']);

        $product->fill([
            'name' => $validated['name'],
            'slug' => $product->exists ? ($slugBase.'-'.$product->id) : ($slugBase.'-'.Str::lower(Str::random(5))),
            'description' => $validated['description'] ?? null,
            'composition' => $validated['composition'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $category->id,
            'category' => $category->slug,
            'image' => $imageUrl,
            'secondary_image' => $secondaryImageUrl,
            'color' => $validated['color'] ?? null,
            'gender' => $validated['gender'] ?? 'unisex',
            'size' => $validated['size'] ?? null,
            'available_sizes' => $sizes === [] ? null : $sizes,
            'is_new_collection' => $request->boolean('is_new_collection'),
            'is_limited_edition' => $request->boolean('is_limited_edition'),
            'is_active' => $request->boolean('is_active'),
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
