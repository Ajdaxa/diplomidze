<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category', 32)->default('clothes')->after('slug');
            $table->json('available_sizes')->nullable()->after('size');
            $table->json('display_colors')->nullable()->after('available_sizes');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'available_sizes', 'display_colors']);
        });
    }
};
