<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Không có user nào, đang tạo 5 user mẫu...');
            $users = User::factory(5)->create();
        }

        foreach ($users as $user) {
            Notification::factory()
                ->count(10) // 10 thông báo mỗi user
                ->create([
                    'user_id' => $user->id,
                ]);
        }

        $this->command->info('✅ Đã tạo thông báo mẫu cho người dùng.');
    }
}