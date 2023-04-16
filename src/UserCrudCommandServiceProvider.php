<?php

declare(strict_types=1);

namespace Kiedrowski\UserCrudCommand;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kiedrowski\UserCrudCommand\Commands\CreateCommand;
use Kiedrowski\UserCrudCommand\Repository\UserRepository;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class UserCrudCommandServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateCommand::class,
            ]);
        }
    }
}
