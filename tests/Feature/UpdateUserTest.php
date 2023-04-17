<?php

test('update-user', function () {
    $this->runUpdateUserTest(id: '1', dataToUpdate: ['name' => 'updated name 1']);
});

test('update-user-not-found', function () {
    $this->userRepository
        ->shouldReceive('exists')
        ->with('1')
        ->andReturn(false);

    $this
        ->artisan('user:update 1')
        ->expectsOutput('User not found.');
});

test('update-user-exception-on-update', function () {
    $this->runUpdateUserTest(id: '1', dataToUpdate: ['name' => 'updated name 1'], exception: true);
});

test('update-user-change-password', function () {
    $this->runUpdateUserTest(id: '1', dataToUpdate: ['name' => 'updated name 1', 'password' => 'newpassword123']);
});
