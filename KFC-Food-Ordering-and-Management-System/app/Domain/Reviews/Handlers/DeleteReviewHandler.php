<?php

namespace App\Domain\Reviews\Handlers;

use App\Domain\Reviews\Commands\DeleteReviewCommand;
use App\Models\Review;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;

class DeleteReviewHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var DeleteReviewCommand $command */
        $review = Review::findOrFail($command->reviewId);
        return (bool) $review->delete();
        
    }
}
