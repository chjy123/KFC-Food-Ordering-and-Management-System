<?php
#author’s name： Yew Kai Quan (for testing purposes only)
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

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
