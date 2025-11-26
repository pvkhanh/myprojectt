<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\Image;
// use App\Models\Imageable;
// use App\Models\User;

// class ImageSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // Tạo 10 ảnh mẫu
//         $images = Image::factory(10)->create();

//         // Lấy 3 user đầu để gắn avatar
//         $users = User::take(3)->get();

//         foreach ($users as $user) {
//             $image = $images->random();
//             Imageable::create([
//                 'image_id' => $image->id,
//                 'imageable_id' => $user->id,
//                 'imageable_type' => User::class,
//                 'is_main' => true,
//                 'position' => 1,
//             ]);
//         }
//     }
// }





namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Image;
use App\Models\Imageable;

class ImageSeeder extends Seeder
{
    private int $totalImages = 0;
    private int $totalRelations = 0;
    private array $errors = [];

    public function run(): void
    {
        $startTime = microtime(true);
        $this->command->newLine();
        $this->command->info('🚀 Starting ImageSeeder...');
        $this->command->newLine();

        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            // 1. Cleanup và chuẩn bị thư mục
            $this->cleanupImageFolder();

            // 2. Seed images cho từng model
            $this->seedUserImages();
            $this->seedProductImages();
            $this->seedBannerImages();

            // 3. Commit transaction
            DB::commit();

            // 4. Hiển thị thống kê
            $this->displaySummary($startTime);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Seeder failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());

            // Cleanup nếu có lỗi
            $this->cleanupOnError();
            throw $e;
        }
    }

    /**
     * Xóa và tạo lại thư mục images
     */
    private function cleanupImageFolder(): void
    {
        $this->command->info('📁 Preparing image folder...');

        $publicFolder = 'public/images';
        $storagePath = storage_path("app/{$publicFolder}");

        try {
            // Xóa thư mục cũ nếu tồn tại
            if (File::exists($storagePath)) {
                File::deleteDirectory($storagePath);
                $this->command->line('   ✓ Cleaned old images');
            }

            // Tạo thư mục mới
            File::makeDirectory($storagePath, 0755, true);
            $this->command->line('   ✓ Created images directory');

            // Kiểm tra quyền ghi
            if (!is_writable($storagePath)) {
                throw new \Exception("Directory {$storagePath} is not writable");
            }

            // Kiểm tra symbolic link
            $this->checkSymbolicLink();

        } catch (\Exception $e) {
            $this->command->error("   ✗ Failed to prepare folder: {$e->getMessage()}");
            throw $e;
        }

        $this->command->newLine();
    }

    /**
     * Kiểm tra symbolic link
     */
    private function checkSymbolicLink(): void
    {
        $publicStorage = public_path('storage');

        if (!File::exists($publicStorage)) {
            $this->command->warn('   ⚠ Storage link not found. Run: php artisan storage:link');
        } else {
            $this->command->line('   ✓ Storage link exists');
        }
    }

    /**
     * Tạo ảnh avatar cho Users
     */
    private function seedUserImages(): void
    {
        $this->command->info('👥 Seeding User images...');

        $bar = $this->command->getOutput()->createProgressBar(5);
        $bar->start();

        try {
            User::factory(5)->create()->each(function ($user) use ($bar) {
                try {
                    // Tạo avatar
                    $avatar = Image::factory()
                        ->withType('avatar')
                        ->withSize(400, 400)
                        ->create();

                    // Tạo relation
                    Imageable::create([
                        'image_id' => $avatar->id,
                        'imageable_id' => $user->id,
                        'imageable_type' => User::class,
                        'is_main' => true,
                        'position' => 1,
                    ]);

                    $this->totalImages++;
                    $this->totalRelations++;

                } catch (\Exception $e) {
                    $this->errors[] = "User #{$user->id}: {$e->getMessage()}";
                }

                $bar->advance();
            });

            $bar->finish();
            $this->command->newLine();
            $this->command->line('   ✓ Created 5 users with avatars');

        } catch (\Exception $e) {
            $this->command->error("   ✗ Failed to seed users: {$e->getMessage()}");
            throw $e;
        }

        $this->command->newLine();
    }

    /**
     * Tạo ảnh cho Products (thumbnail + gallery)
     */
    private function seedProductImages(): void
    {
        $this->command->info('📦 Seeding Product images...');

        $totalProducts = 5;
        $bar = $this->command->getOutput()->createProgressBar($totalProducts);
        $bar->start();

        try {
            Product::factory($totalProducts)->create()->each(function ($product) use ($bar) {
                try {
                    // 1. Tạo thumbnail chính
                    $thumbnail = Image::factory()
                        ->withType('thumbnail')
                        ->withSize(800, 800)
                        ->create();

                    Imageable::create([
                        'image_id' => $thumbnail->id,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                        'is_main' => true,
                        'position' => 1,
                    ]);

                    $this->totalImages++;
                    $this->totalRelations++;

                    // 2. Tạo gallery 3-5 ảnh
                    $galleryCount = rand(3, 5);
                    $galleryImages = Image::factory($galleryCount)
                        ->withType('gallery')
                        ->withSize(800, 800)
                        ->create();

                    foreach ($galleryImages as $index => $image) {
                        Imageable::create([
                            'image_id' => $image->id,
                            'imageable_id' => $product->id,
                            'imageable_type' => Product::class,
                            'is_main' => false,
                            'position' => $index + 2,
                        ]);

                        $this->totalImages++;
                        $this->totalRelations++;
                    }

                } catch (\Exception $e) {
                    $this->errors[] = "Product #{$product->id}: {$e->getMessage()}";
                }

                $bar->advance();
            });

            $bar->finish();
            $this->command->newLine();
            $this->command->line('   ✓ Created 5 products with thumbnails and galleries');

        } catch (\Exception $e) {
            $this->command->error("   ✗ Failed to seed products: {$e->getMessage()}");
            throw $e;
        }

        $this->command->newLine();
    }

    /**
     * Tạo ảnh banner
     */
    private function seedBannerImages(): void
    {
        $this->command->info('🎨 Seeding Banner images...');

        $bar = $this->command->getOutput()->createProgressBar(3);
        $bar->start();

        try {
            Banner::factory(3)->create()->each(function ($banner) use ($bar) {
                try {
                    // Tạo banner image (wide format)
                    $bannerImage = Image::factory()
                        ->withType('banner')
                        ->withSize(1920, 600)
                        ->create();

                    Imageable::create([
                        'image_id' => $bannerImage->id,
                        'imageable_id' => $banner->id,
                        'imageable_type' => Banner::class,
                        'is_main' => true,
                        'position' => 1,
                    ]);

                    $this->totalImages++;
                    $this->totalRelations++;

                } catch (\Exception $e) {
                    $this->errors[] = "Banner #{$banner->id}: {$e->getMessage()}";
                }

                $bar->advance();
            });

            $bar->finish();
            $this->command->newLine();
            $this->command->line('   ✓ Created 3 banners');

        } catch (\Exception $e) {
            $this->command->error("   ✗ Failed to seed banners: {$e->getMessage()}");
            throw $e;
        }

        $this->command->newLine();
    }

    /**
     * Hiển thị thống kê kết quả
     */
    private function displaySummary(float $startTime): void
    {
        $duration = round(microtime(true) - $startTime, 2);

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('✅ ImageSeeder completed successfully!');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->newLine();

        // Thống kê cơ bản
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Users', User::count()],
                ['Products', Product::count()],
                ['Banners', Banner::count()],
                ['Total Images', $this->totalImages],
                ['Total Relations', $this->totalRelations],
                ['Database Images', Image::count()],
                ['Database Relations', Imageable::count()],
            ]
        );

        // Thống kê theo type
        $this->command->newLine();
        $this->command->line('📊 Images by type:');
        $imagesByType = Image::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        foreach ($imagesByType as $stat) {
            $this->command->line("   • {$stat->type}: {$stat->total}");
        }

        // Thống kê file
        $this->command->newLine();
        $storagePath = storage_path('app/public/images');
        if (File::exists($storagePath)) {
            $files = File::files($storagePath);
            $totalSize = 0;
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
            $this->command->line('💾 Storage:');
            $this->command->line('   • Files: ' . count($files));
            $this->command->line('   • Size: ' . $this->formatBytes($totalSize));
        }

        // Hiển thị lỗi nếu có
        if (!empty($this->errors)) {
            $this->command->newLine();
            $this->command->warn('⚠ Warnings (' . count($this->errors) . '):');
            foreach ($this->errors as $error) {
                $this->command->line("   • {$error}");
            }
        }

        $this->command->newLine();
        $this->command->info("⏱ Duration: {$duration}s");
        $this->command->info('═══════════════════════════════════════════');
        $this->command->newLine();
    }

    /**
     * Cleanup khi có lỗi
     */
    private function cleanupOnError(): void
    {
        $this->command->warn('🧹 Cleaning up...');

        try {
            $publicFolder = 'public/images';
            $storagePath = storage_path("app/{$publicFolder}");

            if (File::exists($storagePath)) {
                File::deleteDirectory($storagePath);
                $this->command->line('   ✓ Removed incomplete images');
            }
        } catch (\Exception $e) {
            $this->command->error('   ✗ Cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes thành human readable
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
