# Simple Command Bus for Laravel 5.x

[![Build Status](https://travis-ci.org/upgate/laravel-command-bus.svg?branch=master)](https://travis-ci.org/upgate/laravel-command-bus)

## Setup

1. `composer require upgate/laravel-command-bus`
2. Register `Upgate\LaravelCommandBus\CommandBusServiceProvider` as a service provider
3. In your service provider, bind `Upgate\LaravelCommandBus\HandlerResolver` to an implementation of your choice. 

HandlerResolver binding examples:

a) `PatternHandlerResolver`:
```php
$this->app->singleton(
    \Upgate\LaravelCommandBus\HandlerResolver::class,
    function () {
        return new \Upgate\LaravelCommandBus\PatternHandlerResolver(
            '\YourAppNamespace\CommandHandlers\%sHandler'
        );
    }
);
```

b) `MapHandlerResolver`:
```php
use YourAppNamespace\Commands;
use YourAppNamespace\CommandHandlers;
// ...
$this->app->singleton(
    \Upgate\LaravelCommandBus\HandlerResolver::class,
    function () {
        return new \Upgate\LaravelCommandBus\MapHandlerResolver(
            [
                Commands\FooCommand::class => Handlers\FooHandler::class,
                Commands\BarCommand::class => Handlers\BarHandler::class,
                // ...
            ]
        );
    }
);
```

c) Bind your own implementation (must extend `\Upgate\LaravelCommandBus\HandlerResolver`).

## Usage

Simplified example:

```php
// Command
class SignUpCommand {

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
    
    public function email()
    {
        return $this->email;
    }
    
    public function password()
    {
        return $this->password;
    }
}

// Handler
class SignUpHandler {

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function handle(SignUpCommand $command)
    {
        $user = User::signUp($command->email(), $command->password());
        $this->userRepository->store($user);
    }

}

// HTTP Controller
use Upgate\LaravelCommandBus\CommandBus;

class UserController {

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }
    
    public function signUp(Request $request)
    {
        $this->commandBus->execute(new SignUpCommand(
            $request->get('email'),
            $request->get('password')
        ));
    }

}

// Console command
use Upgate\LaravelCommandBus\CommandBus;

class SignUpUserConsoleCommand
{

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }
    
    public function handle()
    {
        $this->commandBus->execute(new SignUpCommand(
            $this->argument('email'),
            $this->argument('password')
        ));
    }
    
}
```

Of course, you might (and should) want to introduce Controller and ConsoleCommand abstract classes with `executeCommand()` methods implemented.
