<?php
#author’s name： Yew Kai Quan (for testing purposes only)
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
        if (App::environment(['local', 'staging'])) {

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                Review::truncate(); 
                Food::truncate();   
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            Category::query()->delete();                
            DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');

            $this->call([
                CategorySeeder::class,
                FoodSeeder::class,
                ReviewSeeder::class,
            ]);
        }
    }
}
