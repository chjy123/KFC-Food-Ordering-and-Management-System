<?php
#author’s name： Lim Jing Min
namespace App\Support\Bus;

class CommandMap {
    public static function map(): array {
        return [
            \App\Domain\Orders\Commands\UpdateOrderStatusCommand::class
                => \App\Domain\Orders\Handlers\UpdateOrderStatusHandler::class,

            \App\Domain\Foods\Commands\CreateFoodCommand::class
                => \App\Domain\Foods\Handlers\CreateFoodHandler::class,
            \App\Domain\Foods\Commands\UpdateFoodCommand::class
                => \App\Domain\Foods\Handlers\UpdateFoodHandler::class,
            \App\Domain\Foods\Commands\DeleteFoodCommand::class
                => \App\Domain\Foods\Handlers\DeleteFoodHandler::class,

            \App\Domain\Reviews\Commands\DeleteReviewCommand::class
                => \App\Domain\Reviews\Handlers\DeleteReviewHandler::class,

        ];
    }
}
