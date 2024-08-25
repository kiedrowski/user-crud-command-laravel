<?php

namespace Kiedrowski\UserCrudCommand\Tests;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\PendingCommand;
use Kiedrowski\UserCrudCommand\UserCrudCommandServiceProvider;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class CreateUserTestCase extends TestCase
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
     * @param  array<string,string>  $additionalFields
     */
    protected function runCreateUserTest(array $additionalFields = [], bool $exception = false): void
    {
        $data = array_merge($this->getRequiredData(), $additionalFields);

        $dataWithHashedPassword = ['password' => $this->hashPassword($data['password'])] + $data;

        $this->hash->shouldReceive('make')
            ->with($data['password'])
            ->andReturn($dataWithHashedPassword['password']);

        $createUser = $this->userRepository
            ->shouldReceive('create')
            ->with($dataWithHashedPassword);
        if ($exception) {
            $createUser->andThrow(new \Exception('test error'));
        } else {
            $createUser->andReturn(1);
        }

        $this->userRepository
            ->shouldReceive('getPrimaryKeyName')
            ->andReturn('id');

        $command = $this
            ->artisan('user:create');

        if (! $command instanceof PendingCommand) {
            $this->fail('Console output isn\'t mock.');
        }

        $command->expectsQuestion('Please type value for field name', $data['name'])
            ->expectsQuestion('Please type value for field email', $data['email'])
            ->expectsQuestion('Please type value for field password', $data['password']);

        if (count($additionalFields)) {
            foreach ($additionalFields as $field => $value) {
                $command = $this->addAdditionalFieldToCommand(
                    command   : $command,
                    fieldName : $field,
                    fieldValue: $value,
                );
            }
        }

        unset($data['password']);

        $columns = array_merge(['id'], array_keys($data));

        $this->userRepository
            ->shouldReceive('findById')
            ->with('1', $columns)
            ->andReturn($data);

        $command
            ->expectsQuestion('Do You want add value for any other field?', false);

        if ($exception) {
            $command->expectsOutput('Something went wrong while creating user.');
            $command->expectsOutput('test error');

            return;
        }

        $command
            ->expectsOutput('User has been created.')
            ->expectsTable($columns, [$data]);
    }

    private function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * @return array<string,string>
     */
    private function getRequiredData(): array
    {
        return [
            'name' => 'test1',
            'email' => 'test1@example.com',
            'password' => 'password123',
        ];
    }

    private function addAdditionalFieldToCommand(
        PendingCommand $command,
        string $fieldName,
        string $fieldValue,
    ): PendingCommand {
        return $command
            ->expectsQuestion('Do You want add value for any other field?', true)
            ->expectsQuestion('Please provide field name', $fieldName)
            ->expectsQuestion('Please provide value for field', $fieldValue);
    }
}
