<?php

test('delete-user', function () {
    $this->userRepository
        ->shouldReceive('delete')
        ->with('1')
        ->andReturn(true);

    $this->userRepository
        ->shouldNotReceive('forceDelete');

    $this->userRepository
        ->shouldReceive('exists')
        ->with('1')
        ->andReturn(true);

     $this
        ->artisan('user:delete 1')
        ->expectsOutput('User has been deleted.');
});

test('delete-user-force', function () {
    $this->userRepository
        ->shouldReceive('forceDelete')
        ->with('1')
        ->andReturn(true);

    $this->userRepository
        ->shouldNotReceive('delete');

    $this->userRepository
        ->shouldReceive('exists')
        ->with('1')
        ->andReturn(true);

    $this
        ->artisan('user:delete 1 --force')
        ->expectsOutput('User has been deleted.');
});

test('delete-user-not-found', function () {
    $this->userRepository
        ->shouldReceive('exists')
        ->with('1')
        ->andReturn(false);

    $this
        ->artisan('user:delete 1')
        ->expectsOutput('User not found.');
});
