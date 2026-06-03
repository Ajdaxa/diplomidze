<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $columns = ['telegram_link_token_expires_at', 'telegram_link_token', 'telegram_chat_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->index()->after('phone');
            }
            if (! Schema::hasColumn('users', 'telegram_link_token')) {
                $table->string('telegram_link_token')->nullable()->unique()->after('telegram_chat_id');
            }
            if (! Schema::hasColumn('users', 'telegram_link_token_expires_at')) {
                $table->timestamp('telegram_link_token_expires_at')->nullable()->after('telegram_link_token');
            }
        });
    }
};
