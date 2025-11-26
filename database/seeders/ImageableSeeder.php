<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\Imageable;

// class ImageableSeeder extends Seeder
// {
//     public function run(): void
//     {
//         Imageable::factory()->count(40)->create();
//     }
// }


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Imageable;

class ImageableSeeder extends Seeder
{
    /**
     * Seeder này không cần thiết nữa vì ImageSeeder đã xử lý việc tạo relations
     * Giữ lại để tham khảo hoặc xóa đi
     */
    public function run(): void
    {
        // Không cần tạo 40 records ngẫu nhiên
        // Vì ImageSeeder đã tạo relations đúng với từng model
        $this->command->info('⚠️  ImageableSeeder is deprecated. Use ImageSeeder instead.');
        $this->command->info('   ImageSeeder handles all image relations automatically.');
    }
}
