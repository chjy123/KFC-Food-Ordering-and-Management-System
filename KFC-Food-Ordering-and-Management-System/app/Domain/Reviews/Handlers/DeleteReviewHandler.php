<?php

namespace App\Domain\Reviews\Handlers;

use App\Domain\Reviews\Commands\DeleteReviewCommand;
use App\Models\Review;
use App\Support\Bus\Command;
use App\Support\Bus\CommandHandler;
use Illuminate\Support\Facades\Auth;

class DeleteReviewHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        /** @var DeleteReviewCommand $command */

        if (!Auth::user()?->role === 'admin') {
        throw new \Illuminate\Auth\Access\AuthorizationException('Admins only');
         }

        $review = Review::findOrFail($command->reviewId);
        return (bool) $review->delete();
        
    }
}
