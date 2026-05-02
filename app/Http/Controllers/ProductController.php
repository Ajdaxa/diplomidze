<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $isFavorite = false;
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
        }

        return view('store.product-show', compact('product', 'isFavorite'));
    }
}
