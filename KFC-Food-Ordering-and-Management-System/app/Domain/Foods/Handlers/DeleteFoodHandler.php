<?php

namespace App\Domain\Foods\Handlers;

use App\Domain\Foods\Commands\DeleteFoodCommand;
use App\Models\Food;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;
use Illuminate\Support\Facades\Storage;

class DeleteFoodHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var DeleteFoodCommand $command */
        $food = Food::findOrFail($command->foodId);

        if ($food->image_url) {
            Storage::disk('public')->delete($food->image_url);
        }

        return (bool) $food->delete(); 
    }
}
