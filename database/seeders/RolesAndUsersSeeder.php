<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
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

            $user = User::query()->firstOrCreate(
                ['email' => "{$role}@dyab.local"],
                [
                    'name' => ucfirst($role),
                    'phone' => '+99450000000'.array_search($role, $roles, true),
                    'role' => $role,
                ]
            );

            $user->syncRoles([$role]);
        }

        Product::query()->firstOrCreate(
            ['slug' => 'silk-midi-dress'],
            [
                'name' => 'Silk Midi Dress',
                'description' => 'Платье из шелка в минималистичном стиле.',
                'price' => 7590,
                'stock' => 12,
                'is_active' => true,
            ]
        );
    }
}
