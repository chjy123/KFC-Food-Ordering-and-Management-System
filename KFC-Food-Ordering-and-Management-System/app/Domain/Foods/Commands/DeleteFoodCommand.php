<?php

namespace App\Domain\Foods\Commands;

use App\Support\Bus\Command;

class DeleteFoodCommand implements Command
{
    public function __construct(
        public int $foodId,
        public int $actorUserId
    ) {}
}
