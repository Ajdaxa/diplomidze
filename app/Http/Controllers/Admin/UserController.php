<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->clients()
            ->withCount([
                'clientOrders',
                'clientOrders as paid_orders_count' => fn ($q) => $q->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered']),
                'reviews',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%'.$search.'%';
                $query->where(function ($q) use ($term): void {
                    $q->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        $this->ensureStoreClient($user);

        $user->loadCount(['clientOrders', 'reviews', 'favoriteProducts']);

        $orders = $user->clientOrders()
            ->withCount('items')
            ->latest()
            ->limit(15)
            ->get();

        $ordersTotal = (float) $user->clientOrders()
            ->whereIn('status', ['paid', 'in_delivery', 'arrived', 'delivered'])
            ->sum('total_price');

        return view('admin.users.show', compact('user', 'orders', 'ordersTotal'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureStoreClient($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'loyalty_points' => ['required', 'integer', 'min:0', 'max:999999'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $payload = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => trim($validated['phone']),
            'loyalty_points' => (int) $validated['loyalty_points'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Данные клиента обновлены.')
            ->with('status_type', 'success');
    }

    private function ensureStoreClient(User $user): void
    {
        abort_unless($user->isStoreClient(), 404);
    }
}
