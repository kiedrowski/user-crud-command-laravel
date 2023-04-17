<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;
use Kiedrowski\UserCrudCommand\Traits\HashPasswordFieldTrait;

class UpdateCommand extends Command
{
    use HashPasswordFieldTrait;

    protected $signature = 'user:update {id}';

    protected $description = 'Update user command.';

    private UserRepositoryInterface $userRepository;

    private Hasher $hash;

    /**
     * @throws UserNotFoundException
     */
    public function handle(
        UserRepositoryInterface $userRepository,
        Hasher $hash,
    ): void {
        $this->userRepository = $userRepository;
        $this->hash = $hash;

        $id = (string) $this->argument('id');

        if (! $this->userRepository->exists($id)) {
            $this->error('User not found.');

            return;
        }

        $inputs = [];

        $inputs = $this->askForFieldsToUpdate($inputs);
        $inputs = $this->hashPasswordField($inputs);

        try {
            $this->userRepository->update($id, $inputs);
        } catch (\Throwable $e) {
            $this->error('Something went wrong while updating user.');
            $this->error($e->getMessage());

            return;
        }

        $selectColumns = $this->getSelectColumns($inputs);

        $user = $this->userRepository->findById($id, $selectColumns);

        $this->info('User has been updated.');
        $this->table($selectColumns, [$user]);
    }

    /**
     * @param  array<string>  $inputs
     * @return array<string>
     */
    private function askForFieldsToUpdate(array $inputs = []): array
    {
        $field = $this->ask('Please provide field name');
        $input = $this->ask('Please provide value for field');

        $inputs[$field] = $input;

        if ($this->confirm('Do You want update any other field?')) {
            return $this->askForFieldsToUpdate($inputs);
        }

        return $inputs;
    }

    /**
     * @param  array<string>  $inputs
     * @return array<string>
     */
    private function getSelectColumns(array $inputs): array
    {
        $columns = $inputs;

        unset($columns[self::DEFAULT_PASSWORD_FIELD]);

        return array_merge([$this->userRepository->getPrimaryKeyName()], array_keys($columns));
    }
}
