<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function offer(): View
    {
        return view('pages.offer');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
