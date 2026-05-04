<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $hitProducts = Product::query()
            ->where('is_active', true)
            ->with('categoryModel')
            ->orderByDesc('is_new_collection')
            ->orderByDesc('is_limited_edition')
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $favoriteIds = [];
        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            $favoriteIds = $user->favoriteProducts()->pluck('products.id')->all();
        }

        return view('store.home', compact('hitProducts', 'favoriteIds'));
    }

    public function catalog(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with('categoryModel')
            ->latest()
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($categories->isEmpty()) {
            $categories = collect(Product::CATEGORIES)->map(fn (string $name, string $slug) => (object) [
                'slug' => $slug,
                'name' => $name,
            ]);
        }

        $favoriteIds = [];
        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            $favoriteIds = $user->favoriteProducts()->pluck('products.id')->all();
        }

        return view('store.catalog', compact('products', 'categories', 'favoriteIds'));
    }
}
