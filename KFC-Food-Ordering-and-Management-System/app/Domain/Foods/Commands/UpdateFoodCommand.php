<?php

namespace App\Domain\Foods\Commands;

use App\Support\Bus\Command;

class UpdateFoodCommand implements Command
{
    public function __construct(
        public int     $foodId,
        public int     $categoryId,
        public string  $name,
        public ?string $description,
        public float   $price,
        public ?bool   $availability,    
        public ?string $newImagePath,    
        public int     $actorUserId
    ) {}
}
