<?php

use Illuminate\Support\Collection;
use Illuminate\Testing\PendingCommand;

test('search-user', function () {
    $usersData = new Collection([
        (object) [
            'id' => '1',
            'name' => 'test',
            'email' => 'test@example.com',
        ],
    ]);

    $this->userRepository
        ->shouldReceive('searchByColumn')
        ->with('id', '1')
        ->andReturn($usersData);

    $command = $this
        ->artisan('user:search id 1');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $usersData = $usersData->map(fn (object $user) => (array) $user);

    $command
        ->expectsTable(array_keys($usersData->first(default: [])), $usersData->toArray());
});

test('search-user-not-found', function () {
    $usersData = new Collection([]);

    $this->userRepository
        ->shouldReceive('searchByColumn')
        ->with('name', 'nonexisting')
        ->andReturn($usersData);

    $command = $this
        ->artisan('user:search name nonexisting');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command->expectsOutput('No users found.');
});
