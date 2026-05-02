<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['admin', 'manager', 'courier', 'client'];

        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');

            $user = User::query()->updateOrCreate(
                ['email' => "{$role}@dyab.local"],
                [
                    'name' => ucfirst($role),
                    'phone' => '+99450000000'.array_search($role, $roles, true),
                    'role' => $role,
                    'password' => Hash::make('Admin12345!'),
                ]
            );

            $user->syncRoles([$role]);
        }

        Product::query()->updateOrCreate(
            ['slug' => 'silk-midi-dress'],
            [
                'name' => 'Silk Midi Dress',
                'description' => 'Платье из шелка в минималистичном стиле.',
                'price' => 7590,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
                'secondary_image' => 'https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?auto=format&fit=crop&w=900&q=80',
                'color' => 'black',
                'size' => 'M',
                'is_new_collection' => true,
                'is_limited_edition' => false,
                'is_active' => true,
            ]
        );

        Product::query()->updateOrCreate(
            ['slug' => 'premium-wool-jacket'],
            [
                'name' => 'Premium Wool Jacket',
                'description' => 'Структурный жакет из премиальной шерсти.',
                'price' => 11490,
                'stock' => 2,
                'image' => 'https://images.unsplash.com/photo-1495385794356-15371f348c31?auto=format&fit=crop&w=900&q=80',
                'secondary_image' => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80',
                'color' => 'wine',
                'size' => 'S',
                'is_new_collection' => false,
                'is_limited_edition' => true,
                'is_active' => true,
            ]
        );
    }
}
