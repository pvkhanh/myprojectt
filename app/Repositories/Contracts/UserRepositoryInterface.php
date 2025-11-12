<?php

// namespace App\Repositories\Contracts;

// use App\Models\User;
// use App\Models\Image;
// use Illuminate\Database\Eloquent\Collection;

// interface UserRepositoryInterface extends RepositoryInterface
// {
//     public function setAvatar(User $user, Image $image): Image;
//     public function getActive(): Collection;
//     public function getByRole(string $role): Collection;
//     public function getByGender(string $gender): Collection;
//     public function search(string $keyword): Collection;
//     public function getVerified(): Collection;
//     public function createdBetween(string $from, string $to): Collection;
    
// }
namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * ========================
     * 🎨 AVATAR MANAGEMENT
     * ========================
     */

    /**
     * Cập nhật avatar trực tiếp trong cột `users.avatar`
     */
    public function updateAvatar(User $user, UploadedFile $file): ?string;

    /**
     * Xóa avatar (trong storage + cập nhật DB)
     */
    public function removeAvatar(User $user): bool;

    /**
     * Gắn avatar thông qua bảng imageables (cũ)
     */
    public function setAvatar(User $user, Image $image): Image;

    /**
     * ========================
     * 🔍 SEARCH & FILTER
     * ========================
     */
    public function search(string $keyword): Collection;
    public function searchPaginated(?string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * ========================
     * 🎯 FILTERS
     * ========================
     */
    public function getActive(): Collection;
    public function getByRole(string $role): Collection;
    public function getByGender(string $gender): Collection;
    public function getVerified(): Collection;
    public function createdBetween(string $from, string $to): Collection;

    /**
     * ========================
     * 🧺 SOFT DELETE
     * ========================
     */
    public function delete(int $id): bool;
    public function forceDelete(int $id): bool;
    public function restore(int $id): bool;
    public function findTrashed(int $id): ?User;
}