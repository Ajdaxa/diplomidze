<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('categoryModel');

        $isFavorite = false;
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
        }

        $reviews = $product->approvedReviews()
            ->with('user:id,name')
            ->latest()
            ->limit(20)
            ->get();

        $averageRating = $product->averageRating();
        $reviewsCount = $product->approvedReviews()->count();

        $userReview = null;
        $canReview = false;
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $userReview = Review::query()
                ->where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->first();
            $canReview = $user->clientOrders()
                ->where('status', 'delivered')
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        $galleryUrls = $product->galleryUrls();

        return view('store.product-show', compact(
            'product',
            'isFavorite',
            'reviews',
            'averageRating',
            'reviewsCount',
            'userReview',
            'canReview',
            'galleryUrls',
        ));
    }
}
