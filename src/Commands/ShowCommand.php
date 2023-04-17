<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class ShowCommand extends Command
{
    protected $signature = 'user:show {id}';

    protected $description = 'Show user command.';

    public function handle(UserRepositoryInterface $userRepository): void
    {
        $id = (string) $this->argument('id');

        try {
            $user = $userRepository->findById($id);
        } catch (UserNotFoundException $e) {
            $this->info($e->getMessage());

            return;
        }

        $this->table(array_keys($user), [$user]);
    }
}
