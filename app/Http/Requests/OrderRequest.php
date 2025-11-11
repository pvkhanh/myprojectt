<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\OrderStatus;

class OrderRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện request này không.
     */
    public function authorize(): bool
    {
        // Nếu dùng middleware admin, có thể trả về true
        return true;
    }

    /**
     * Các rule validate
     */
    public function rules(): array
    {
        $rules = [];

        $action = $this->route()?->getActionMethod();

        switch ($action) {
            case 'update':
            case 'updateStatus':
                $rules = [
                    'status' => 'required|in:' . implode(',', OrderStatus::values()),
                    'admin_note' => 'nullable|string|max:500',
                ];
                break;

            case 'cancel':
            case 'rejectPayment':
                $rules = [
                    'reason' => 'required|string|max:500',
                ];
                break;

            case 'confirmPayment':
                $rules = [
                    'note' => 'nullable|string|max:500',
                ];
                break;

            case 'store':
                // Nếu có chức năng tạo đơn mới
                $rules = [
                    'user_id' => 'required|exists:users,id',
                    'order_items' => 'required|array|min:1',
                    'order_items.*.product_id' => 'required|exists:products,id',
                    'order_items.*.variant_id' => 'nullable|exists:product_variants,id',
                    'order_items.*.quantity' => 'required|integer|min:1',
                    'shipping_address_id' => 'required|exists:shipping_addresses,id',
                    'status' => 'nullable|in:' . implode(',', OrderStatus::values()),
                    'admin_note' => 'nullable|string|max:500',
                ];
                break;

            default:
                $rules = [];
        }

        return $rules;
    }

    /**
     * Messages tuỳ chỉnh
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái đơn hàng không được để trống.',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'reason.required' => 'Lý do không được để trống.',
            'reason.max' => 'Lý do không được vượt quá 500 ký tự.',
            'admin_note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
            'order_items.required' => 'Danh sách sản phẩm không được để trống.',
            'order_items.*.product_id.required' => 'Sản phẩm không hợp lệ.',
            'order_items.*.quantity.required' => 'Số lượng sản phẩm không được để trống.',
            'order_items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
            'order_items.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
        ];
    }
}