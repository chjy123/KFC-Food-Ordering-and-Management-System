<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;
use App\Models\Review;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // ❌ remove: Review::truncate();

        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        foreach (Food::all() as $food) {
            Review::create([
                'food_id' => $food->id,
                'user_id' => $user->id,
                'rating'  => rand(3,5),
                'comment' => "Really enjoyed the {$food->name}!",
            ]);
        }
    }
}
