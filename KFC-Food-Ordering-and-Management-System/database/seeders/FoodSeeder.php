<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;
use App\Models\Category;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        // ❌ remove: Food::truncate();

        $main = Category::where('category_name', 'Main Dishes')->first();
        $bev  = Category::where('category_name', 'Beverages')->first();
        $des  = Category::where('category_name', 'Desserts')->first();

        $foods = [
    [
        'category_id' => $main->id,
        'name' => 'Fried Chicken',
        'description' => 'Crispy and juicy',
        'price' => 12.90,
        'availability' => true,
        'image_url' => 'https://kfc.com.my/media/catalog/product/9/-/9-pc-chicken-combo_1.jpg?quality=80&bg-color=255,255,255&fit=bounds&height=&width='
    ],
    [
        'category_id' => $bev->id,
        'name' => 'Iced Latte',
        'description' => 'Chilled espresso',
        'price' => 8.50,
        'availability' => true,
        'image_url' => 'https://kfc.com.my/media/catalog/product/9/-/9-pc-chicken-combo_1.jpg?quality=80&bg-color=255,255,255&fit=bounds&height=&width='
    ],
    [
        'category_id' => $des->id,
        'name' => 'Cheesecake',
        'description' => 'Creamy & sweet',
        'price' => 9.90,
        'availability' => true,
        'image_url' => 'https://kfc.com.my/media/catalog/product/9/-/9-pc-chicken-combo_1.jpg?quality=80&bg-color=255,255,255&fit=bounds&height=&width='
    ],
];

        foreach ($foods as $food) {
            Food::create($food);
        }
    }
}
