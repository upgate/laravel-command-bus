<?php
declare(strict_types=1);

namespace Upgate\LaravelCommandBus;

abstract class HandlerResolver
{

    /**
     * @param object $command
     * @return string
     * @throws Exception\HandlerNotFound
     */
    abstract public function getHandlerClass($command): string;

}