<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Food;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Put production-safe seeds here (roles/admin). Leave empty if none.

        // 2) Local/Staging demo data only
        if (App::environment(['local', 'staging'])) {

            // --- Clear children with TRUNCATE (OK) ---
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                Review::truncate(); // reviews.food_id -> foods.id
                Food::truncate();   // foods.category_id -> categories.id
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // --- Clear parent WITHOUT TRUNCATE ---
            Category::query()->delete();                 // <-- no truncate here
            DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');

            // --- Seed demo data ---
            $this->call([
                CategorySeeder::class,
                FoodSeeder::class,
                ReviewSeeder::class,
            ]);
        }
    }
}
