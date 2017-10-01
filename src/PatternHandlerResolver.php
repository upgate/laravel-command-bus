<?php
declare(strict_types=1);

namespace Upgate\LaravelCommandBus;

final class PatternHandlerResolver extends HandlerResolver
{

    const DEFAULT_COMMAND_SUFFIX = 'Command';

    /**
     * @var string
     */
    private $pattern;
    /**
     * @var string
     */
    private $commandSuffix;

    public function __construct(string $pattern, string $commandSuffix = self::DEFAULT_COMMAND_SUFFIX)
    {
        if (strpos($pattern, '%s') === false) {
            throw new \InvalidArgumentException('%s placeholder not found in $pattern');
        }
        $this->pattern = $pattern;
        $this->commandSuffix = $commandSuffix;
    }

    /**
     * @param $command
     * @return string
     * @throws Exception\HandlerNotFound
     */
    public function getHandlerClass($command): string
    {
        $commandClassBasename = preg_replace(
            '/' . preg_quote($this->commandSuffix, '/') . '$/',
            '',
            basename(str_replace('\\', '/', get_class($command)))
        );

        $handlerClass = sprintf($this->pattern, $commandClassBasename);
        if (!class_exists($handlerClass)) {
            throw new Exception\HandlerNotFound("Handler not found for command: " . get_class($command));
        }

        return $handlerClass;
    }

}