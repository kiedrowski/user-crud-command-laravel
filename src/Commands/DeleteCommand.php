<?php

namespace Kiedrowski\UserCrudCommand\Commands;

use Illuminate\Console\Command;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

class DeleteCommand extends Command
{
    protected $signature = 'user:delete {id} {--force}';

    protected $description = 'Delete user command.';

    public function handle(UserRepositoryInterface $userRepository): void
    {
        $id = (string) $this->argument('id');

        if (! $userRepository->exists($id)) {
            $this->error('User not found.');

            return;
        }

        $force = (bool) $this->option('force');

        try {
            if ($force) {
                $userRepository->forceDelete($id);
            } else {
                $userRepository->delete($id);
            }
        } catch (\Throwable $e) {
            $this->error('Something went wrong while deleting user.');
            $this->error($e->getMessage());

            return;
        }

        $this->info('User has been deleted.');
    }
}
