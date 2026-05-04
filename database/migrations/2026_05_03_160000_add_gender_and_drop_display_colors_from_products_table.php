<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('gender', 16)->default('unisex')->after('color');
            $table->dropColumn('display_colors');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('display_colors')->nullable()->after('available_sizes');
            $table->dropColumn('gender');
        });
    }
};
