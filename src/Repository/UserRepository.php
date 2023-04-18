<?php

namespace Kiedrowski\UserCrudCommand\Repository;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    private Model $model;

    public function __construct(
        private readonly Container $container,
        private readonly ConnectionInterface $connection,
        private readonly Config $config,
    ) {
        $this->model = $this->container->make(
            $this->config->get('usercrud.model_class', UserRepositoryInterface::DEFAULT_USER_MODEL_CLASS)
        );
    }

    public function create(array $values): string|int
    {
        return $this->getQueryBuilder()
            ->insertGetId($values);
    }

    public function createUsingModel(array $values): string|int
    {
        return $this->model->create($values)->{$this->getPrimaryKeyName()};
    }

    public function update(string|int $id, array $values): int
    {
        return $this->getQueryBuilder()
            ->where($this->getPrimaryKeyName(), '=', $id)
            ->limit(1)
            ->update($values);
    }

    public function findById(string|int $id, array $columns = ['*']): array
    {
        $row = $this->getQueryBuilder()
            ->where($this->getPrimaryKeyName(), '=', $id)
            ->first($columns);

        if (! $row) {
            throw new UserNotFoundException($this->getPrimaryKeyName(), $id);
        }

        return (array) $row;
    }

    public function searchByColumn(string $column, string $value): Collection
    {
        return $this->getQueryBuilder()
            ->where($column, 'like', "%{$value}%")
            ->get();
    }

    public function exists(string|int $id): bool
    {
        return $this->getQueryBuilder()
            ->where($this->getPrimaryKeyName(), '=', $id)
            ->exists();
    }

    public function delete(int|string $id): bool
    {
        if ($columnName = $this->getSoftDeleteColumn()) {
            return (bool) $this->getQueryBuilder()
                ->where($this->getPrimaryKeyName(), '=', $id)
                ->update([
                    $columnName => now(),
                ]);
        }

        return $this->forceDelete($id);
    }

    public function forceDelete(int|string $id): bool
    {
        return (bool) $this->getQueryBuilder()
            ->where($this->getPrimaryKeyName(), '=', $id)
            ->delete();
    }

    public function getPrimaryKeyName(): string
    {
        return $this->model->getKeyName();
    }

    private function getTableName(): string
    {
        return $this->model->getTable();
    }

    private function getSoftDeleteColumn(): string|null
    {
        if ($this->isSoftDeletedUsed()) {
            return $this->model->getDeletedAtColumn(); // @phpstan-ignore-line
        }

        return null;
    }

    private function isSoftDeletedUsed(): bool
    {
        return method_exists($this->model, 'getDeletedAtColumn');
    }

    private function getQueryBuilder(): Builder
    {
        return $this->connection->table($this->getTableName());
    }
}
