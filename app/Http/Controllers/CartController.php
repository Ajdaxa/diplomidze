<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = collect($cart)
            ->map(function ($qty, $productId) use ($products) {
                $product = $products->get((int) $productId);
                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => (int) $qty,
                    'line_total' => $product->price * $qty,
                ];
            })
            ->filter()
            ->values();

        $total = $items->sum('line_total');

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Product $product)
    {
        $cart = session('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + 1;
        session(['cart' => $cart]);

        return back()->with('status', 'Товар добавлен в корзину.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:20']]);

        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id] = (int) $request->integer('quantity');
        }
        session(['cart' => $cart]);

        return back()->with('status', 'Корзина обновлена.');
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('status', 'Товар удален из корзины.');
    }
}
