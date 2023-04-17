<?php

namespace Kiedrowski\UserCrudCommand\Tests;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\PendingCommand;
use Kiedrowski\UserCrudCommand\UserCrudCommandServiceProvider;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class UpdateUserTestCase extends TestCase
{
    protected LegacyMockInterface|MockInterface $hash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hash = Mockery::mock(Hasher::class);
        $this->instance(Hasher::class, $this->hash);
    }

    protected function getPackageProviders($app): array
    {
        return [
            UserCrudCommandServiceProvider::class,
        ];
    }

    /**
     * @param  array<string,string>  $dataToUpdate
     */
    protected function runUpdateUserTest(string|int $id, array $dataToUpdate = [], bool $exception = false): void
    {
        $this->userRepository
            ->shouldReceive('exists')
            ->with($id)
            ->andReturn(true);

        $this->userRepository
            ->shouldReceive('getPrimaryKeyName')
            ->andReturn('id');

        $command = $this
            ->artisan("user:update {$id}");

        if (! $command instanceof PendingCommand) {
            $this->fail('Console output isn\'t mock.');
        }

        if (count($dataToUpdate)) {
            foreach ($dataToUpdate as $field => $value) {
                $command = $this->addFieldToCommand(
                    command   : $command,
                    fieldName : $field,
                    fieldValue: $value,
                );

                $command->expectsQuestion(
                    'Do You want update any other field?',
                    array_key_last($dataToUpdate) !== $field
                );
            }
        }

        if (isset($dataToUpdate['password'])) {
            $this->hash->shouldReceive('make')
                ->with($dataToUpdate['password'])
                ->andReturn($hashedPassword = Hash::make($dataToUpdate['password']));

            $dataToUpdate['password'] = $hashedPassword;
        }

        $updateUser = $this->userRepository
            ->shouldReceive('update')
            ->with($id, $dataToUpdate);

        if ($exception) {
            $updateUser->andThrow(new \Exception('test error'));
        } else {
            $updateUser->andReturn(1);
        }

        unset($dataToUpdate['password']);

        $columns = array_merge(['id'], array_keys($dataToUpdate));

        $this->userRepository
            ->shouldReceive('findById')
            ->with($id, $columns)
            ->andReturn($dataToUpdate);

        if ($exception) {
            $command->expectsOutput('Something went wrong while updating user.');
            $command->expectsOutput('test error');

            return;
        }

        $command
            ->expectsOutput('User has been updated.')
            ->expectsTable($columns, [$dataToUpdate]);
    }

    private function addFieldToCommand(
        PendingCommand $command,
        string $fieldName,
        string $fieldValue,
    ): PendingCommand {
        return $command
            ->expectsQuestion('Please provide field name', $fieldName)
            ->expectsQuestion('Please provide value for field', $fieldValue);
    }
}
