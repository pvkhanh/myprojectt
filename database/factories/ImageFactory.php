<?php

// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;

// class ImageFactory extends Factory
// {
//     public function definition(): array
//     {
//         return [
//             'path' => $this->faker->imageUrl(800, 800, 'nature', true, 'image'),
//             'type' => $this->faker->randomElement(['avatar', 'banner', 'thumbnail', 'gallery']),
//             'alt_text' => $this->faker->sentence(3),
//             'is_active' => $this->faker->boolean(90),
//         ];
//     }
// }


// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;
// use Illuminate\Support\Facades\Storage;
// use App\Models\Image;

// class ImageFactory extends Factory
// {
//     protected $model = Image::class;

//     public function definition(): array
//     {
//         $folder = 'images';
//         $type = $this->faker->randomElement(['avatar', 'banner', 'thumbnail', 'gallery']);

//         // Đảm bảo thư mục tồn tại
//         $storagePath = storage_path("app/public/{$folder}");
//         if (!file_exists($storagePath)) {
//             mkdir($storagePath, 0755, true);
//         }

//         // Tạo ảnh và lấy tên file (không có path)
//         $fileName = $this->faker->image(
//             $storagePath,  // Thư mục đích
//             800,           // Width
//             800,           // Height
//             'cats',        // Category (null, cats, city, food, nightlife, fashion, people, nature, sports, technics, transport)
//             false          // Trả về tên file thay vì full path
//         );

//         return [
//             'path' => "{$folder}/{$fileName}",  // Lưu đường dẫn relative trong DB
//             'type' => $type,
//             'alt_text' => $this->faker->sentence(3),
//             'is_active' => $this->faker->boolean(95),
//         ];
//     }

//     /**
//      * Tạo ảnh với type cụ thể
//      */
//     public function withType(string $type): static
//     {
//         return $this->state(fn(array $attributes) => [
//             'type' => $type,
//         ]);
//     }

//     /**
//      * Tạo ảnh với kích thước tùy chỉnh
//      */
//     public function withSize(int $width, int $height): static
//     {
//         return $this->state(function (array $attributes) use ($width, $height) {
//             $folder = 'images';
//             $storagePath = storage_path("app/public/{$folder}");

//             if (!file_exists($storagePath)) {
//                 mkdir($storagePath, 0755, true);
//             }

//             $fileName = $this->faker->image(
//                 $storagePath,
//                 $width,
//                 $height,
//                 'cats',
//                 false
//             );

//             return [
//                 'path' => "{$folder}/{$fileName}",
//             ];
//         });
//     }
// }
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Image;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        $folder = 'images';
        $type = $this->faker->randomElement(['avatar', 'banner', 'thumbnail', 'gallery']);

        // Đảm bảo thư mục tồn tại
        $storagePath = storage_path("app/public/{$folder}");
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Tạo ảnh với fallback mechanism
        $fileName = $this->generateImage($storagePath, 800, 800);

        return [
            'path' => "{$folder}/{$fileName}",
            'type' => $type,
            'alt_text' => $this->faker->sentence(3),
            'is_active' => $this->faker->boolean(95),
        ];
    }

    /**
     * Tạo ảnh với type cụ thể
     */
    public function withType(string $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Tạo ảnh với kích thước tùy chỉnh
     */
    public function withSize(int $width, int $height): static
    {
        return $this->state(function (array $attributes) use ($width, $height) {
            $folder = 'images';
            $storagePath = storage_path("app/public/{$folder}");

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $fileName = $this->generateImage($storagePath, $width, $height);

            return [
                'path' => "{$folder}/{$fileName}",
            ];
        });
    }

    /**
     * Generate image với nhiều methods khác nhau
     */
    private function generateImage(string $path, int $width, int $height): string
    {
        // Method 1: Thử dùng Faker's image() (cần internet)
        try {
            $fileName = $this->faker->image(
                $path,
                $width,
                $height,
                'cats',
                false,
                true,
                'Faker'
            );

            if ($fileName && file_exists($path . '/' . $fileName)) {
                return $fileName;
            }
        } catch (\Exception $e) {
            // Tiếp tục với method khác
        }

        // Method 2: Download từ picsum.photos
        try {
            $fileName = $this->downloadPlaceholder($path, $width, $height);
            if ($fileName) {
                return $fileName;
            }
        } catch (\Exception $e) {
            // Tiếp tục với method khác
        }

        // Method 3: Tạo ảnh placeholder bằng GD
        try {
            $fileName = $this->createPlaceholderWithGD($path, $width, $height);
            if ($fileName) {
                return $fileName;
            }
        } catch (\Exception $e) {
            // Tiếp tục với method khác
        }

        // Method 4: Tạo ảnh đơn giản nhất
        return $this->createSimplePlaceholder($path, $width, $height);
    }

    /**
     * Download ảnh từ picsum.photos
     */
    private function downloadPlaceholder(string $path, int $width, int $height): ?string
    {
        $fileName = Str::random(40) . '.jpg';
        $filePath = $path . '/' . $fileName;

        // URL picsum.photos
        $url = "https://picsum.photos/{$width}/{$height}";

        // Download với timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0'
            ]
        ]);

        $imageData = @file_get_contents($url, false, $context);

        if ($imageData && file_put_contents($filePath, $imageData)) {
            return $fileName;
        }

        return null;
    }

    /**
     * Tạo placeholder bằng GD library
     */
    private function createPlaceholderWithGD(string $path, int $width, int $height): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $fileName = Str::random(40) . '.jpg';
        $filePath = $path . '/' . $fileName;

        // Tạo canvas
        $image = imagecreatetruecolor($width, $height);

        // Random background color
        $bgColor = imagecolorallocate(
            $image,
            rand(100, 255),
            rand(100, 255),
            rand(100, 255)
        );
        imagefill($image, 0, 0, $bgColor);

        // Text color
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Add text
        $text = "{$width}x{$height}";
        $fontSize = min($width, $height) / 10;

        // Thử dùng TTF font nếu có
        $fontFile = public_path('fonts/arial.ttf');
        if (file_exists($fontFile)) {
            $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $textWidth = abs($textBox[4] - $textBox[0]);
            $textHeight = abs($textBox[5] - $textBox[1]);
            $x = ($width - $textWidth) / 2;
            $y = ($height + $textHeight) / 2;
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $text);
        } else {
            // Fallback: dùng built-in font
            $x = ($width - (strlen($text) * 8)) / 2;
            $y = ($height - 16) / 2;
            imagestring($image, 5, $x, $y, $text, $textColor);
        }

        // Add some random shapes
        for ($i = 0; $i < 5; $i++) {
            $shapeColor = imagecolorallocate(
                $image,
                rand(50, 200),
                rand(50, 200),
                rand(50, 200)
            );

            imagefilledellipse(
                $image,
                rand(0, $width),
                rand(0, $height),
                rand(50, 150),
                rand(50, 150),
                $shapeColor
            );
        }

        // Save
        $result = imagejpeg($image, $filePath, 85);
        imagedestroy($image);

        return $result ? $fileName : null;
    }

    /**
     * Tạo placeholder đơn giản nhất (1x1 pixel)
     */
    private function createSimplePlaceholder(string $path, int $width, int $height): string
    {
        $fileName = Str::random(40) . '.jpg';
        $filePath = $path . '/' . $fileName;

        // Tạo ảnh 1x1 pixel đơn giản
        if (extension_loaded('gd')) {
            $image = imagecreatetruecolor(1, 1);
            $color = imagecolorallocate($image, 200, 200, 200);
            imagefill($image, 0, 0, $color);
            imagejpeg($image, $filePath, 85);
            imagedestroy($image);
        } else {
            // Nếu không có GD, tạo file text đại diện
            file_put_contents($filePath, "Placeholder {$width}x{$height}");
        }

        return $fileName;
    }
}