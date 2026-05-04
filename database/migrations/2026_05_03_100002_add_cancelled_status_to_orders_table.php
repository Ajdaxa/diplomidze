<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','paid','in_delivery','arrived','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
        // SQLite: колонка уже хранится как строка — дополнительное значение допустимо без ALTER ENUM.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'cancelled'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','paid','in_delivery','arrived','delivered') NOT NULL DEFAULT 'pending'");
        }
    }
};
