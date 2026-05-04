<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $sort = 0;
        foreach (Product::CATEGORIES as $slug => $name) {
            Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'sort_order' => $sort++,
                    'is_active' => true,
                ]
            );
        }

        foreach (Category::query()->get() as $category) {
            Product::query()->where('category', $category->slug)->update(['category_id' => $category->id]);
        }
    }
}
