<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra nếu column chưa tồn tại thì mới thêm
        if (!Schema::hasColumn('payments', 'payment_gateway')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_gateway')->nullable()->after('payment_method');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'payment_gateway')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_gateway');
            });
        }
    }
};
