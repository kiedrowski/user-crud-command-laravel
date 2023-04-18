<?php

namespace Kiedrowski\UserCrudCommand\Repository;

use Illuminate\Support\Collection;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;

interface UserRepositoryInterface
{
    public const DEFAULT_USER_MODEL_CLASS = '\\App\\Models\\User';

    /**
     * @param  array<string, string|int>  $values
     */
    public function create(array $values): int|string;

    /**
     * @param  array<string, string|int>  $values
     */
    public function createUsingModel(array $values): int|string;

    /**
     * @param  array<string, string|int>  $values
     */
    public function update(string|int $id, array $values): int;

    /**
     * @param  array<string>  $columns
     * @return array<string, string|int>
     *
     * @throws UserNotFoundException
     */
    public function findById(string|int $id, array $columns = ['*']): array;

    /**
     * @return Collection<int, object>
     */
    public function searchByColumn(string $column, string $value): Collection;

    public function exists(string|int $id): bool;

    public function getPrimaryKeyName(): string;
}
