<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

// interface CartItemRepositoryInterface extends BaseRepositoryInterface
// {
//     public function getByUser(int $userId): Collection;
//     public function addOrUpdate(int $userId, int $productId, int $quantity, ?int $variantId = null): Model;
//     public function clearUserCart(int $userId): int;
//     public function selectedForUser(int $userId): Collection;
// }




interface CartItemRepositoryInterface
{
    /**
     * Lấy tất cả items trong giỏ hàng của user.
     */
    public function getByUser(int $userId): Collection;

    /**
     * Lấy các items đã chọn để checkout.
     */
    public function selectedForUser(int $userId): Collection;

    /**
     * Tìm cart item theo ID và user (bảo mật).
     */
    public function findByUser(int $itemId, int $userId): ?Model;

    /**
     * Tìm cart item theo product và variant.
     */
    public function findByProductAndVariant(int $userId, int $productId, ?int $variantId = null): ?Model;

    /**
     * Thêm hoặc cập nhật cart item (cộng dồn quantity).
     */
    public function addOrUpdate(int $userId, int $productId, int $quantity, ?int $variantId = null): Model;

    /**
     * Cập nhật cart item (return Model).
     */
    public function updateCartItem(int $id, array $data): ?Model;

    /**
     * Xóa cart item (force delete).
     */
    public function delete(int $id): bool;

    /**
     * Xóa toàn bộ giỏ hàng của user.
     */
    public function clearUserCart(int $userId): int;

    /**
     * Xóa các items đã chọn.
     */
    public function deleteSelectedItems(int $userId): int;

    /**
     * Toggle trạng thái selected của cart item.
     */
    public function toggleSelected(int $itemId, int $userId): bool;

    /**
     * Chọn/bỏ chọn tất cả items.
     */
    public function selectAll(int $userId, bool $selectAll = true): int;

    /**
     * Đếm số lượng items trong giỏ hàng.
     */
    public function countByUser(int $userId): int;

    /**
     * Đếm số lượng items đã chọn.
     */
    public function countSelectedByUser(int $userId): int;

    /**
     * Tính tổng số lượng sản phẩm (sum quantity).
     */
    public function getTotalQuantity(int $userId): int;

    /**
     * Tính tổng số lượng sản phẩm đã chọn.
     */
    public function getSelectedTotalQuantity(int $userId): int;

    /**
     * Kiểm tra có items đã chọn không.
     */
    public function hasSelectedItems(int $userId): bool;

    /**
     * Xóa các items không khả dụng (hết hàng/inactive).
     */
    public function removeUnavailableItems(int $userId): int;

    /**
     * Điều chỉnh quantity về tồn kho tối đa.
     */
    public function adjustQuantityToStock(int $userId): int;

    /**
     * Validate giỏ hàng trước checkout.
     */
    public function validateForCheckout(int $userId): array;
}