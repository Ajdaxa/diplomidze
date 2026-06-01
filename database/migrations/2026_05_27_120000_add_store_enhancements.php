<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('secondary_image');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('loyalty_points')->default(0)->after('role');
            $table->string('referral_code', 32)->nullable()->unique()->after('loyalty_points');
            $table->foreignId('referred_by_user_id')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
        });

        Schema::table('promocodes', function (Blueprint $table) {
            $table->string('purpose', 20)->default('standard')->after('code');
            $table->foreignId('user_id')->nullable()->after('purpose')->constrained()->nullOnDelete();
            $table->decimal('min_order_total', 10, 2)->nullable()->after('max_discount');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->unique(['product_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');

        Schema::table('promocodes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['purpose', 'min_order_total']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_user_id');
            $table->dropColumn(['loyalty_points', 'referral_code']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('gallery_images');
        });
    }
};
