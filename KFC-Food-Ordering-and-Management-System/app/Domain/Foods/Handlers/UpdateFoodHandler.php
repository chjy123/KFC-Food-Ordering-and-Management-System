<?php

namespace App\Domain\Foods\Handlers;

use App\Domain\Foods\Commands\UpdateFoodCommand;
use App\Models\Food;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UpdateFoodHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var UpdateFoodCommand $command */
        $food = Food::findOrFail($command->foodId);
        
        if (!Auth::user()?->role === 'admin') {
        throw new \Illuminate\Auth\Access\AuthorizationException('Admins only');
         }

        $food->fill([
            'category_id' => $command->categoryId,
            'name'        => $command->name,
            'description' => $command->description,
            'price'       => $command->price,
        ]);

        if ($command->availability !== null) {
            $food->availability = $command->availability;
        }

        if ($command->newImagePath) {
            // delete old file if exists, then replace
            if ($food->image_url) {
                Storage::disk('public')->delete($food->image_url);
            }
            $food->image_url = $command->newImagePath; // 'foods/xxx.jpg'
        }

        $food->save();
        return $food->fresh();
    }
}
