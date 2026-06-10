<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Listrik'],
            ['name' => 'Plastik'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
