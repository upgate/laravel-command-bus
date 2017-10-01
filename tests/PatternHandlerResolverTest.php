<?php
declare(strict_types=1);

namespace {

    use Upgate\LaravelCommandBus\Exception\HandlerNotFound;
    use Upgate\LaravelCommandBus\PatternHandlerResolver;
    use PHPUnit\Framework\TestCase;

    class PatternHandlerResolverTest extends TestCase
    {

        public function testHandlerTemplateWithoutNamespace()
        {
            $resolver = new PatternHandlerResolver('%sHandler');
            $handlerClass = $resolver->getHandlerClass(new FooCommand());
            $this->assertEquals(FooHandler::class, $handlerClass);
        }

        public function testHandlerTemplateNamespace()
        {
            $resolver = new PatternHandlerResolver('PatternHandlerResolverTestNs\\%sHandler');
            $handlerClass = $resolver->getHandlerClass(new FooCommand());
            $this->assertEquals(PatternHandlerResolverTestNs\FooHandler::class, $handlerClass);
        }

        public function testCommandClassBasenameIsUsed()
        {
            $resolver = new PatternHandlerResolver('%sHandler');
            $handlerClass = $resolver->getHandlerClass(new PatternHandlerResolverTestNs\FooCommand());
            $this->assertEquals(FooHandler::class, $handlerClass);
        }

        public function testCommandClassCustomSuffix()
        {
            $resolver = new PatternHandlerResolver('%sHandler', 'Cmd');
            $handlerClass = $resolver->getHandlerClass(new FooCmd());
            $this->assertEquals(FooHandler::class, $handlerClass);
        }

        public function testCommandClassEmptySuffix()
        {
            $resolver = new PatternHandlerResolver('%sHandler', '');
            $handlerClass = $resolver->getHandlerClass(new Foo());
            $this->assertEquals(FooHandler::class, $handlerClass);
        }

        public function testExceptionIsThrownWhenNoHandlerMatchesCommand()
        {
            $resolver = new PatternHandlerResolver('%sHandler');
            $this->expectException(HandlerNotFound::class);
            $resolver->getHandlerClass(new BarCommand());
        }

        public function testExceptionIsThrownForInvalidTemplate()
        {
            $this->expectException(\InvalidArgumentException::class);
            new PatternHandlerResolver('Handler');
        }

    }

    class FooCommand
    {
    }

    class BarCommand
    {
    }

    class FooCmd
    {
    }

    class Foo
    {
    }

    class FooBar
    {
    }

    class FooHandler
    {
    }
}

namespace PatternHandlerResolverTestNs {

    class FooCommand
    {
    }

    class FooHandler
    {
    }

}

