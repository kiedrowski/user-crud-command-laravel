<?php

namespace Kiedrowski\UserCrudCommand\Repository;

use Illuminate\Support\Collection;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;

interface UserRepositoryInterface
{
    public const DEFAULT_USER_MODEL_CLASS = '\\App\\Models\\User';

    /**
     * @param  array<string, mixed>  $values
     */
    public function create(array $values): int|string;

    /**
     * @param  array<string, mixed>  $values
     */
    public function update(string|int $id, array $values): bool;

    /**
     * @param  array<string>  $columns
     * @return array<string, mixed>
     *
     * @throws UserNotFoundException
     */
    public function findById(string|int $id, array $columns = ['*']): array;

    /**
     * @return Collection<int, array>
     */
    public function searchByColumn(string $column, string $value): Collection;

    /**
     * @return Collection<int, array>
     */
    public function all(int $limit): Collection;

    public function exists(string|int $id): bool;

    public function delete(string|int $id): bool;

    public function forceDelete(string|int $id): bool;

    public function getPrimaryKeyName(): string;
}
