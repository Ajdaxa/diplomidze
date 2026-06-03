<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(25);

        $pendingCount = Review::query()->where('status', Review::STATUS_PENDING)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => Review::STATUS_APPROVED]);

        return back()->with('status', 'Отзыв опубликован.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('status', 'Отзыв удалён.');
    }
}
