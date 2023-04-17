<?php

use Illuminate\Testing\PendingCommand;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;

test('show-user', function () {
    $userData = [
        'id' => 1,
        'name' => 'test',
        'email' => 'test@example.com',
    ];

    $this->userRepository
        ->shouldReceive('findById')
        ->with($userData['id'])
        ->andReturn($userData);

    $command = $this
        ->artisan('user:show 1');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command
        ->expectsTable(array_keys($userData), [$userData]);
});

test('show-user-not-found', function () {
    $idColumn = 'id';
    $nonExistingId = '2';

    $this->userRepository
        ->shouldReceive('findById')
        ->with($nonExistingId)
        ->andThrows(new UserNotFoundException($idColumn, $nonExistingId));

    $command = $this
        ->artisan("user:show {$nonExistingId}");

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command
        ->expectsOutput("User with {$idColumn} {$nonExistingId} not found.");
});
