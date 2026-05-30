<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 32)->nullable()->unique()->after('slug');
        });

        Product::query()->with('categoryModel')->orderBy('id')->each(function (Product $product) {
            if (filled($product->sku)) {
                return;
            }
            $product->updateQuietly(['sku' => Product::generateSku($product)]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
