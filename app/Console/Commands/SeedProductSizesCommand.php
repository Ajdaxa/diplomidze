<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\ProductSizeStockGenerator;
use Illuminate\Console\Command;

class SeedProductSizesCommand extends Command
{
    protected $signature = 'products:seed-sizes';

    protected $description = 'Реалистично заполнить size_stock и available_sizes для всех товаров';

    public function handle(): int
    {
        $count = 0;

        Product::query()->with('categoryModel')->chunkById(50, function ($products) use (&$count): void {
            foreach ($products as $product) {
                $generated = ProductSizeStockGenerator::forProduct($product);

                $product->updateQuietly([
                    'size_stock' => $generated['size_stock'],
                    'available_sizes' => $generated['available_sizes'],
                    'size' => $generated['size'],
                ]);

                $count++;
            }
        });

        $this->info("Обновлено товаров: {$count}");

        return self::SUCCESS;
    }
}
