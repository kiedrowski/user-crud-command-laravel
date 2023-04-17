<?php

namespace Kiedrowski\UserCrudCommand\Tests;

use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;
use Kiedrowski\UserCrudCommand\UserCrudCommandServiceProvider;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected LegacyMockInterface|MockInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->instance(UserRepositoryInterface::class, $this->userRepository);
    }

    protected function getPackageProviders($app): array
    {
        return [
            UserCrudCommandServiceProvider::class,
        ];
    }
}
