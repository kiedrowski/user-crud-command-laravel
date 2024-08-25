<?php

declare(strict_types=1);

namespace Kiedrowski\UserCrudCommand;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kiedrowski\UserCrudCommand\Commands\CreateCommand;
use Kiedrowski\UserCrudCommand\Commands\DeleteCommand;
use Kiedrowski\UserCrudCommand\Commands\ListCommand;
use Kiedrowski\UserCrudCommand\Commands\SearchCommand;
use Kiedrowski\UserCrudCommand\Commands\ShowCommand;
use Kiedrowski\UserCrudCommand\Commands\UpdateCommand;
use Kiedrowski\UserCrudCommand\Repository\UserModelRepository;
use Kiedrowski\UserCrudCommand\Repository\UserRepository;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class UserCrudCommandServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/usercrud.php' => config_path('usercrud.php'),
        ]);

        $this->app->bind(
            UserRepositoryInterface::class,
            $this->app->config['usercrud.use_model_repository'] ?? false
                ? UserModelRepository::class
                : UserRepository::class,
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateCommand::class,
                ShowCommand::class,
                UpdateCommand::class,
                SearchCommand::class,
                DeleteCommand::class,
                ListCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/usercrud.php', 'usercrud'
        );
    }
}
