<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AdminHubController extends Controller
{
    public function __invoke()
    {
        $pendingReviews = Review::query()->where('status', Review::STATUS_PENDING)->count();

        return view('admin.hub', compact('pendingReviews'));
    }
}
