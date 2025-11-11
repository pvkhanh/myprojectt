<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // ✅ Cột cho biết thanh toán này có cần xác minh thủ công hay không (VD: COD thì cần)
            $table->boolean('requires_manual_verification')->default(false)->after('status');

            // ✅ Đã được xác minh chưa (true = đã xác minh, false = chưa)
            $table->boolean('is_verified')->default(false)->after('requires_manual_verification');

            // ✅ Thời điểm xác minh thanh toán (nullable vì có thể chưa được xác minh)
            $table->timestamp('verified_at')->nullable()->after('is_verified');

            // ✅ ID của người xác minh (liên kết tới bảng users)
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');

            // ✅ Ghi chú khi xác minh (VD: “Đã đối soát thành công” hoặc “Sai mã giao dịch”)
            $table->text('verification_note')->nullable()->after('verified_by');

            // ✅ Tên cổng thanh toán (VD: vnpay, momo, stripe...) — phục vụ phân biệt nguồn giao dịch
            $table->string('payment_gateway')->nullable()->after('payment_method');

            // ✅ Dữ liệu phản hồi chi tiết từ cổng thanh toán (lưu JSON: transaction_id, bank_code,...)
            $table->json('gateway_response')->nullable()->after('payment_gateway');
            
            // ✅ Khóa ngoại liên kết đến người dùng xác minh, nếu bị xóa thì set null
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // 🔄 Xóa khóa ngoại trước khi xóa cột
            $table->dropForeign(['verified_by']);

            // 🔄 Xóa toàn bộ các cột vừa thêm ở trên (khi rollback)
            $table->dropColumn([
                'requires_manual_verification', // Cần xác minh thủ công
                'is_verified',                  // Đã xác minh hay chưa
                'verified_at',                  // Thời điểm xác minh
                'verified_by',                  // Ai xác minh
                'verification_note',            // Ghi chú xác minh
                'payment_gateway',              // Tên cổng thanh toán
                'gateway_response'              // Phản hồi JSON từ cổng thanh toán
            ]);
        });
    }
};