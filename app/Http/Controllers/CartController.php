<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CartPricing;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartSession(): array
    {
        return session('cart', []);
    }

    private function lineKey(int $productId, string $size): string
    {
        return $productId.'|'.strtoupper(trim($size));
    }

    public function index()
    {
        $items = CartPricing::buildFromSession($this->cartSession());
        $summary = CartPricing::summarize($items);

        return view('cart.index', [
            'items' => $items,
            'total' => $summary['total'],
            'catalogSubtotal' => $summary['catalog_subtotal'],
            'productDiscount' => $summary['product_discount'],
            'subtotal' => $summary['subtotal'],
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => ['required', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $size = strtoupper(trim($validated['size']));
        $allowed = array_map('strtoupper', $product->sizesList());
        if (! in_array($size, $allowed, true)) {
            return back()->withErrors(['size' => 'Выберите доступный размер.']);
        }

        $qty = (int) ($validated['quantity'] ?? 1);
        $key = $this->lineKey($product->id, $size);
        $cart = $this->cartSession();
        $currentQty = (int) ($cart[$key] ?? 0);
        if ($product->stock < 1) {
            return back()->withErrors(['stock' => 'Товар закончился на складе.']);
        }
        if (($currentQty + $qty) > $product->stock) {
            return back()->withErrors(['stock' => 'Нельзя добавить больше, чем есть на складе.']);
        }

        $cart[$key] = ($cart[$key] ?? 0) + $qty;
        session(['cart' => $cart]);

        return back()
            ->with('status', 'Добавлено в корзину.')
            ->with('status_type', 'success');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $key = $this->lineKey($product->id, $validated['size']);
        $cart = $this->cartSession();
        if (array_key_exists($key, $cart)) {
            $cart[$key] = (int) $validated['quantity'];
            session(['cart' => $cart]);
        }

        return back()
            ->with('status', 'Корзина обновлена.')
            ->with('status_type', 'info');
    }

    public function remove(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => ['required', 'string', 'max:20'],
        ]);

        $key = $this->lineKey($product->id, $validated['size']);
        $cart = $this->cartSession();
        unset($cart[$key]);
        session(['cart' => $cart]);

        return back()
            ->with('status', 'Товар удален из корзины.')
            ->with('status_type', 'warn');
    }
}
