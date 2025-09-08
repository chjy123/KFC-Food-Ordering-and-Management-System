<?php

namespace App\Domain\Foods\Handlers;

use App\Domain\Foods\Commands\CreateFoodCommand;
use App\Models\Food;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;

class CreateFoodHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var CreateFoodCommand $command */
        return Food::create([
            'category_id' => $command->categoryId,
            'name'        => $command->name,
            'description' => $command->description,
            'price'       => $command->price,
            'availability'=> $command->availability,
            'image_url'   => $command->imagePath,   
        ]);
    }
}
