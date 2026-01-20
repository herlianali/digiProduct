<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fonts', 'slug' => 'fonts'],
            ['name' => 'Templates', 'slug' => 'templates'],
            ['name' => 'Illustrations', 'slug' => 'illustrations'],
            ['name' => 'UI Kits', 'slug' => 'ui-kits'],
            ['name' => 'Others', 'slug' => 'others'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}