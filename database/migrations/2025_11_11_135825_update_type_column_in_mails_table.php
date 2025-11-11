<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm 'order' vào enum type của cột mails.type
        DB::statement("ALTER TABLE `mails` MODIFY `type` ENUM('system','user','marketing','order') NOT NULL DEFAULT 'system'");
    }

    public function down(): void
    {
        // Loại bỏ 'order' nếu rollback
        DB::statement("ALTER TABLE `mails` MODIFY `type` ENUM('system','user','marketing') NOT NULL DEFAULT 'system'");
    }
};
