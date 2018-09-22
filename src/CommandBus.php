<?php
declare(strict_types=1);

namespace Upgate\LaravelCommandBus;

use Illuminate\Contracts\Container\Container;

final class CommandBus
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var HandlerResolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $handlersCache = [];

    public function __construct(Container $container, HandlerResolver $resolver)
    {
        $this->container = $container;
        $this->resolver = $resolver;
    }

    /**
     * @param object $command
     * @throws \Upgate\LaravelCommandBus\Exception\HandlerNotFound
     */
    public function execute($command): void
    {
        $this->executeSync($command);
    }

    /**
     * @param object $command
     * @throws \Upgate\LaravelCommandBus\Exception\HandlerNotFound
     */
    public function executeSync($command)
    {
        if (!is_object($command)) {
            throw new \InvalidArgumentException("Command is expected to be an object");
        }
        $handlerClass = $this->resolver->getHandlerClass($command);
        // We want handlers to be able to declare handle method with typehints
        // e.g. handle(SomeCommand $command). With no generics in PHP, there's no way
        // to make an abstract class or an interface for a handler to allow that. So we're
        // stuck with this stupid way.
        if (!method_exists($handlerClass, 'handle')) {
            throw new \LogicException("Handler " . $handlerClass . " does not implement handle()");
        }
        if (!isset($this->handlersCache[$handlerClass])) {
            $this->handlersCache[$handlerClass] = $this->container->make($handlerClass);
        }
        $handler = $this->handlersCache[$handlerClass];

        return $handler->handle($command);
    }

}
