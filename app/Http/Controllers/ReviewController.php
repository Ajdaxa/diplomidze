<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $hasDelivered = Auth::user()
            ->clientOrders()
            ->where('status', 'delivered')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        if (! $hasDelivered) {
            return back()->withErrors([
                'review' => 'Отзыв можно оставить после доставки этого товара.',
            ]);
        }

        Review::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $validated['rating'],
                'body' => $validated['body'] ?? null,
                'status' => Review::STATUS_PENDING,
            ]
        );

        return back()->with('status', 'Спасибо! Отзыв отправлен на модерацию.');
    }
}
