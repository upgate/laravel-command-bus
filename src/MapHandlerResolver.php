<?php
declare(strict_types=1);

namespace Upgate\LaravelCommandBus;

final class MapHandlerResolver extends HandlerResolver
{

    /**
     * @var array
     */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param $command
     * @return string
     */
    public function getHandlerClass($command): string
    {
        $handlerClass = $this->map[get_class($command)] ?? null;

        if (!is_string($handlerClass) || !class_exists($handlerClass)) {
            throw new Exception\HandlerNotFound("Handler not found for command: " . get_class($command));
        }

        return $handlerClass;
    }

}