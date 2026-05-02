<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminHubController extends Controller
{
    public function __invoke()
    {
        return view('admin.hub');
    }
}
