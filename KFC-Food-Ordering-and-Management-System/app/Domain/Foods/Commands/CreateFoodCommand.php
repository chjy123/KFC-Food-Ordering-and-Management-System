<?php

namespace App\Domain\Foods\Commands;

use App\Support\Bus\Command;

class CreateFoodCommand implements Command
{
    public function __construct(
        public int     $categoryId,
        public string  $name,
        public ?string $description,
        public float   $price,
        public bool    $availability,    // true on create (your current behavior)
        public ?string $imagePath,       // e.g. 'foods/abc.jpg' (relative to public disk)
        public int     $actorUserId
    ) {}
}
