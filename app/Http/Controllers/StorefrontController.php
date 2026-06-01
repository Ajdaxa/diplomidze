<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\CatalogQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function catalog(Request $request): View
    {
        $filters = CatalogQuery::filtersFromRequest($request);
        $products = CatalogQuery::apply(CatalogQuery::base(), $filters)->get();

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

        $priceBounds = Product::query()
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('store.catalog', compact('products', 'categories', 'favoriteIds', 'filters', 'priceBounds'));
    }
}
