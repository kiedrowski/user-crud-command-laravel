<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class ListCommand extends Command
{
    protected $signature = 'user:list {limit=0}';

    protected $description = 'List users command.';

    public function handle(UserRepositoryInterface $userRepository): void
    {
        $limit = (int) $this->argument('limit');

        try {
            $users = $userRepository
                ->all($limit)
                ->map(fn (object $user) => (array) $user);
        } catch (\Throwable $e) {
            $this->error('Something went wrong while listing users.');
            $this->error($e->getMessage());

            return;
        }

        if ($users->isNotEmpty()) {
            $this->table(array_keys($users->first(default: [])), $users->toArray());

            return;
        }

        $this->info('No users found.');
    }
}
