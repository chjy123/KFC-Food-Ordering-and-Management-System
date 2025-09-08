<?php

namespace App\Domain\Reviews\Commands;

use App\Support\Bus\Command;

class DeleteReviewCommand implements Command
{
    public function __construct(
        public int $reviewId,
        public int $actorUserId 
    ) {}
}
