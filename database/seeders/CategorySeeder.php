<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $positionCounter = 1;

        $roots = Category::factory()->count(10)->create();

        $roots->each(function ($root) use (&$positionCounter) {
            $root->update(['position' => $positionCounter++]);

            $children = Category::factory()->count(rand(2, 4))->child($root)->create();

            $children->each(function ($child) use (&$positionCounter) {
                $child->update(['position' => $positionCounter++]);
            });
        });
    }

}