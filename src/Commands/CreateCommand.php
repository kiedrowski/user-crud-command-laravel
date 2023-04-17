<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Hashing\Hasher;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;
use Kiedrowski\UserCrudCommand\Traits\HashPasswordFieldTrait;

class CreateCommand extends Command
{
    use HashPasswordFieldTrait;

    public const DEFAULT_REQUIRED_FIELDS = ['name', 'email', self::DEFAULT_PASSWORD_FIELD];

    protected $signature = 'user:create';

    protected $description = 'Create user command.';

    private UserRepositoryInterface $userRepository;

    private Config $config;

    private Hasher $hash;

    public function handle(
        UserRepositoryInterface $userRepository,
        Config $config,
        Hasher $hash,
    ): void {
        $this->userRepository = $userRepository;
        $this->config = $config;
        $this->hash = $hash;

        $inputs = [];

        $inputs = $this->askForRequiredFields($inputs);
        $inputs = $this->askForAdditionalFields($inputs);
        $inputs = $this->hashPasswordField($inputs);

        $useModel = $this->confirm('Do You want create user using eloquent user model?');

        try {
            $id = $useModel
                ? $this->userRepository->createUsingModel($inputs)
                : $this->userRepository->create($inputs);
        } catch (\Throwable $e) {
            $this->error('Something went wrong while creating user.');
            $this->error($e->getMessage());

            return;
        }

        $selectColumns = $this->getSelectColumns($inputs);

        $user = $this->userRepository->findById($id, $selectColumns);

        $this->info('User has been created.');
        $this->table($selectColumns, [$user]);
    }

    /**
     * @param  array<string>  $inputs
     * @return array<string>
     */
    private function askForRequiredFields(array $inputs = []): array
    {
        foreach ($this->getRequiredFields() as $requiredField) {
            $input = $this->ask("Please type value for field {$requiredField}");

            $inputs[$requiredField] = $input;
        }

        return $inputs;
    }

    /**
     * @param  array<string>  $inputs
     * @return array<string>
     */
    private function askForAdditionalFields(array $inputs = []): array
    {
        if ($this->confirm('Do You want add value for any other field?')) {
            $field = $this->ask('Please provide field name');
            $input = $this->ask('Please provide value for field');

            $inputs[$field] = $input;

            return $this->askForAdditionalFields($inputs);
        }

        return $inputs;
    }

    /**
     * @return array<string>
     */
    private function getRequiredFields(): array
    {
        return $this->config->get('usercrud.required_fields', static::DEFAULT_REQUIRED_FIELDS);
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
