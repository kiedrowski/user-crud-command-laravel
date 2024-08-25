<?php

use Illuminate\Support\Collection;
use Illuminate\Testing\PendingCommand;

test('search-user', function () {
    $usersData = new Collection([
        [
            'id' => '1',
            'name' => 'test',
            'email' => 'test@example.com',
        ],
    ]);

    $this->userRepository
        ->shouldReceive('all')
        ->with(1)
        ->andReturn($usersData);

    $command = $this
        ->artisan('user:list 1');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command
        ->expectsTable(array_keys($usersData->first(default: [])), $usersData->toArray());

    $command->doesntExpectOutput('No users found.');
});

test('list-user-not-found', function () {
    $usersData = new Collection([]);

    $this->userRepository
        ->shouldReceive('all')
        ->with(1)
        ->andReturn($usersData);

    $command = $this
        ->artisan('user:list 1');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command->expectsOutput('No users found.');
});
