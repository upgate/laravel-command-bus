<?php
declare(strict_types=1);

namespace Upgate\LaravelCommandBus;

use Illuminate\Support\ServiceProvider;

class CommandBusServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(CommandBus::class, CommandBus::class);
    }

}