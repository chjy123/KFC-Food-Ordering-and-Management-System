<?php

namespace App\Domain\Orders\Commands;

use App\Support\Bus\Command;

class UpdateOrderStatusCommand implements Command
{
    public function __construct(
        public int $orderId,
        public int $actorUserId
    ) {}
}
