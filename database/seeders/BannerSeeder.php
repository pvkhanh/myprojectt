<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 banner bằng factory
        Banner::factory()->count(10)->create();
    }
}
