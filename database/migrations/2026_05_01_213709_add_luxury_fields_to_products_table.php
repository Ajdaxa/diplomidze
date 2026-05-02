<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('secondary_image')->nullable()->after('image');
            $table->string('color', 50)->nullable()->after('secondary_image');
            $table->string('size', 20)->nullable()->after('color');
            $table->boolean('is_new_collection')->default(false)->after('size');
            $table->boolean('is_limited_edition')->default(false)->after('is_new_collection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_image',
                'color',
                'size',
                'is_new_collection',
                'is_limited_edition',
            ]);
        });
    }
};
