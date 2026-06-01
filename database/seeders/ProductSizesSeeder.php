<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Support\ProductSizeStockGenerator;
use Illuminate\Database\Seeder;

class ProductSizesSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->with('categoryModel')->each(function (Product $product): void {
            $generated = ProductSizeStockGenerator::forProduct($product);

            $product->updateQuietly([
                'size_stock' => $generated['size_stock'],
                'available_sizes' => $generated['available_sizes'],
                'size' => $generated['size'],
            ]);
        });
    }
}
