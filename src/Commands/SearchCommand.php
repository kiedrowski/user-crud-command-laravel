<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class SearchCommand extends Command
{
    protected $signature = 'user:search {column} {value}';

    protected $description = 'Search user command.';

    public function handle(UserRepositoryInterface $userRepository): void
    {
        $column = (string) $this->argument('column');
        $value = (string) $this->argument('value');

        try {
            $users = $userRepository
                ->searchByColumn($column, $value)
                ->map(fn(object $user) => (array) $user);
        } catch (\Throwable $e) {
            $this->error('Something went wrong while searching user.');
            $this->error($e->getMessage());

            return;
        }

        if ($users->isNotEmpty()) {
            $this->table(array_keys($users->first()), $users->toArray());
        }

        $this->info('No users found.');
    }
}
