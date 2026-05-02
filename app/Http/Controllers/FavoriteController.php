<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $products = $user
            ->favoriteProducts()
            ->where('is_active', true)
            ->latest('favorite_products.created_at')
            ->get();

        return view('store.favorites', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        $user = $request->user();
        $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();

        if ($isFavorite) {
            $user->favoriteProducts()->detach($product->id);

            return response()->json([
                'status' => 'removed',
                'message' => 'Убрано из избранного.',
            ]);
        }

        $user->favoriteProducts()->syncWithoutDetaching([$product->id]);

        return response()->json([
            'status' => 'added',
            'message' => 'Добавлено в избранное.',
        ]);
    }
}
