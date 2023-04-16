<?php

namespace Kiedrowski\UserCrudCommand\Repository;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class UserRepository implements UserRepositoryInterface
{
    private Model $model;

    private Builder $queryBuilder;

    public function __construct(
        private readonly Container $container,
        private readonly ConnectionInterface $connection,
        private readonly Config $config,
    ) {
        $this->model = $this->container->make(
            $this->config->get('usercrud.model_class', UserRepositoryInterface::DEFAULT_USER_MODEL_CLASS)
        );

        $this->queryBuilder = $this->connection->table($this->getTableName());
    }

    public function create(array $values): string|int
    {
        return $this->queryBuilder
            ->insertGetId($values);
    }

    public function createUsingModel(array $values): string|int
    {
        return $this->model->create($values)->{$this->getPrimaryKeyName()};
    }

     public function findById(string|int $id, array $columns = ['*']): array
     {
        return (array) $this->queryBuilder
            ->where($this->getPrimaryKeyName(), '=', $id)
            ->first($columns);
    }

    public function getPrimaryKeyName(): string
    {
        return $this->model->getKeyName();
    }

    private function getTableName(): string
    {
        return $this->model->getTable();
    }
}
