<?php
declare(strict_types=1);

use Upgate\LaravelCommandBus\MapHandlerResolver;
use PHPUnit\Framework\TestCase;

class MapHandlerResolverTest extends TestCase
{

    public function testHandlerIsFoundInMap()
    {
        $resolver = new MapHandlerResolver(
            [MapHandlerResolverTestFooCommand::class => MapHandlerResolverTestFooHandler::class]
        );
        $handlerClass = $resolver->getHandlerClass(new MapHandlerResolverTestFooCommand());
        $this->assertEquals(MapHandlerResolverTestFooHandler::class, $handlerClass);
    }

    public function testExceptionIsThrownWhenNoHandlerMatchesCommand()
    {
        $resolver = new MapHandlerResolver(
            [MapHandlerResolverTestFooCommand::class => MapHandlerResolverTestFooHandler::class]
        );
        $this->expectException(\Upgate\LaravelCommandBus\Exception\HandlerNotFound::class);
        $resolver->getHandlerClass(new MapHandlerResolverTestBarCommand());
    }

    public function testExceptionIsThrownWhenClassDoesNotExist()
    {
        /** @noinspection PhpUndefinedClassInspection */
        $resolver = new MapHandlerResolver(
            [MapHandlerResolverTestFooCommand::class => MapHandlerResolverTestUNDEFINED::class]
        );
        $this->expectException(\Upgate\LaravelCommandBus\Exception\HandlerNotFound::class);
        $resolver->getHandlerClass(new MapHandlerResolverTestBarCommand());
    }

}

class MapHandlerResolverTestFooCommand
{
}

class MapHandlerResolverTestBarCommand
{
}

class MapHandlerResolverTestFooHandler
{
}
