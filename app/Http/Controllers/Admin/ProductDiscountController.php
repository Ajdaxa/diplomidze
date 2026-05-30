<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDiscountController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->with('categoryModel')
            ->orderByDesc('sale_percent')
            ->orderBy('name')
            ->get();

        $onSale = $products->filter(fn (Product $p) => $p->hasSale());

        return view('admin.sales.index', compact('products', 'onSale'));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'sale_percent' => ['nullable', 'integer', 'min:0', 'max:90'],
            'action' => ['nullable', 'in:apply,clear'],
        ]);

        $percent = ($validated['action'] ?? 'apply') === 'clear'
            ? null
            : (isset($validated['sale_percent']) && (int) $validated['sale_percent'] > 0
                ? (int) $validated['sale_percent']
                : null);

        if (($validated['action'] ?? 'apply') === 'apply' && $percent === null) {
            return back()->withErrors(['sale_percent' => 'Укажите размер скидки от 1 до 90%.']);
        }

        $count = Product::query()
            ->whereIn('id', $validated['product_ids'])
            ->update(['sale_percent' => $percent]);

        $message = $percent
            ? "Скидка −{$percent}% применена к {$count} товарам."
            : "Скидка снята с {$count} товаров.";

        return redirect()
            ->route('admin.sales.index')
            ->with('status', $message);
    }
}
