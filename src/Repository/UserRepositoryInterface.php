<?php

namespace Kiedrowski\UserCrudCommand\Repository;

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
     * @return array<array<string|int>>
     */
    public function searchByColumn(string $column, string $value): array;

    public function exists(string|int $id): bool;

    public function getPrimaryKeyName(): string;
}
