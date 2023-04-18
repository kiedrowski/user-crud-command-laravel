<?php

use Illuminate\Testing\PendingCommand;

test('search-user', function () {
    $userData = [
        [
            'id' => '1',
            'name' => 'test',
            'email' => 'test@example.com',
        ],
    ];

    $this->userRepository
        ->shouldReceive('searchByColumn')
        ->with('id', '1')
        ->andReturn($userData);

    $command = $this
        ->artisan('user:search id 1');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command
        ->expectsTable(array_keys($userData[array_key_first($userData)]), $userData);
});

test('search-user-not-found', function () {
    $userData = [];

    $this->userRepository
        ->shouldReceive('searchByColumn')
        ->with('name', 'nonexisting')
        ->andReturn($userData);

    $command = $this
        ->artisan('user:search name nonexisting');

    if (! $command instanceof PendingCommand) {
        $this->fail('Console output isn\'t mock.');
    }

    $command->expectsOutput('No users found.');
});
