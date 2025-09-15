<?php
#author’s name： Lim Jing Min
namespace App\Support\Bus;
interface CommandHandler {
    public function handle(Command $command);
}
