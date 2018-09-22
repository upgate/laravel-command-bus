<?php
declare(strict_types=1);

use Upgate\LaravelCommandBus\CommandBus;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Container\Container;

class CommandBusTest extends TestCase
{

    public function testExecute()
    {
        $resolver = $this->getMockBuilder(\Upgate\LaravelCommandBus\HandlerResolver::class)->getMock();
        $resolver->method('getHandlerClass')->willReturn(CommandBusTestHandler::class);
        /** @var \Upgate\LaravelCommandBus\HandlerResolver $resolver */

        $container = $this->getMockBuilder(Container::class)->getMock();
        $container->method('make')->with(CommandBusTestHandler::class)->willReturn(new CommandBusTestHandler());
        /** @var Illuminate\Contracts\Container\Container $container */

        $bus = new CommandBus($container, $resolver);
        $cmd = new CommandBusTestCommand();
        $bus->execute($cmd);
        $this->assertTrue($cmd->handled);
    }

    public function testExecuteSync()
    {
        $resolver = $this->getMockBuilder(\Upgate\LaravelCommandBus\HandlerResolver::class)->getMock();
        $resolver->method('getHandlerClass')->willReturn(CommandBusTestHandler::class);
        /** @var \Upgate\LaravelCommandBus\HandlerResolver $resolver */

        $container = $this->getMockBuilder(Container::class)->getMock();
        $container->method('make')->with(CommandBusTestHandler::class)->willReturn(new CommandBusTestHandler());
        /** @var Illuminate\Contracts\Container\Container $container */

        $bus = new CommandBus($container, $resolver);
        $cmd = new CommandBusTestCommand();
        $this->assertEquals("result", $bus->executeSync($cmd));
        $this->assertTrue($cmd->handled);
    }

    public function testExceptionIsThrownIfHandlerHasNoHandleMethod()
    {
        $resolver = $this->getMockBuilder(\Upgate\LaravelCommandBus\HandlerResolver::class)->getMock();
        $resolver->method('getHandlerClass')->willReturn(\stdClass::class);
        /** @var \Upgate\LaravelCommandBus\HandlerResolver $resolver */

        $container = $this->getMockBuilder(Container::class)->getMock();
        $container->method('make')->with(\stdClass::class)->willReturn(new \stdClass());
        /** @var Illuminate\Contracts\Container\Container $container */

        $bus = new CommandBus($container, $resolver);
        $cmd = new CommandBusTestCommand();
        $this->expectException(\LogicException::class);
        $bus->execute($cmd);
    }

    public function testHandlerIsInstaniatedOnce()
    {
        $resolver = $this->getMockBuilder(\Upgate\LaravelCommandBus\HandlerResolver::class)->getMock();
        $resolver->method('getHandlerClass')->willReturn(CommandBusTestHandler::class);
        /** @var \Upgate\LaravelCommandBus\HandlerResolver $resolver */

        $container = $this->getMockBuilder(Container::class)->getMock();
        $container->method('make')->with(CommandBusTestHandler::class)->willReturnCallback(
            function () {
                return new CommandBusTestHandler();
            }
        );
        /** @var Illuminate\Contracts\Container\Container $container */

        $bus = new CommandBus($container, $resolver);

        CommandBusTestHandler::$instancesCounter = 0;

        $cmd = new CommandBusTestCommand();
        $bus->execute($cmd);

        $this->assertEquals(1, CommandBusTestHandler::$instancesCounter);

        $cmd = new CommandBusTestCommand();
        $bus->execute($cmd);

        $this->assertEquals(1, CommandBusTestHandler::$instancesCounter);
    }

}

class CommandBusTestCommand
{
    public $handled = false;
}

class CommandBusTestHandler
{

    public static $instancesCounter = 0;

    public function __construct()
    {
        self::$instancesCounter++;
    }

    public function handle(CommandBusTestCommand $command)
    {
        $command->handled = true;

        return "result";
    }

}
