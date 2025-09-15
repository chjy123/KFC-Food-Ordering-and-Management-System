<?php
namespace App\Support\Bus;
#author’s name： Lim Jing Min
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommandBus {
    public function __construct(private Container $container) {}
    public function dispatch(Command $command) {
        $map = CommandMap::map();
        $class = $command::class;
        if (!isset($map[$class])) {
            throw new \RuntimeException("No handler for {$class}");
        }
        $handler = $this->container->make($map[$class]);
        return DB::transaction(function () use ($handler, $command) {
            Log::info('Command', ['name' => $command::class, 'data' => get_object_vars($command)]);
            return $handler->handle($command);
        });
    }
}
