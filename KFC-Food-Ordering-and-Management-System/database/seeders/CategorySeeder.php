<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ❌ remove: Category::truncate();

        $categories = [
            ['category_name' => 'Main Dishes', 'description' => 'Delicious main courses'],
            ['category_name' => 'Beverages',   'description' => 'Refreshing drinks'],
            ['category_name' => 'Desserts',    'description' => 'Sweet and tasty desserts'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
