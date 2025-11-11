<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\User;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $authors = User::where('role', 'admin')->get();

        foreach ($authors as $author) {
            Blog::factory()->count(5)->create([
                'author_id' => $author->id,
                'status' => 'published',
            ]);
        }
    }
}