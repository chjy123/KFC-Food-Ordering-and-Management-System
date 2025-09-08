<?php
namespace App\Support\Bus;
interface CommandHandler {
    public function handle(Command $command);
}
