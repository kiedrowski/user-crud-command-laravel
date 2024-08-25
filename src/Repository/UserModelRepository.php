<?php

namespace Kiedrowski\UserCrudCommand\Repository;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kiedrowski\UserCrudCommand\Exceptions\UserNotFoundException;

class UserModelRepository implements UserRepositoryInterface
{
    private Model $model;

    public function __construct(
        private readonly Container $container,
        private readonly Config $config,
    ) {
        $this->model = $this->container->make(
            $this->config->get('usercrud.model_class', UserRepositoryInterface::DEFAULT_USER_MODEL_CLASS),
        );
    }

    public function create(array $values): string|int
    {
        return $this->model->create($values)->{$this->getPrimaryKeyName()};
    }

    public function update(string|int $id, array $values): bool
    {
        return (bool) $this->model->find($id)?->update($values);
    }

    public function findById(string|int $id, array $columns = ['*']): array
    {
        $row = $this->model->find($id, $columns);

        if ($row === null) {
            throw new UserNotFoundException($this->getPrimaryKeyName(), $id);
        }

        return $row->toArray();
    }

    public function searchByColumn(string $column, string $value): Collection
    {
        return $this->model
            ->where($column, 'like', "%{$value}%")
            ->get()
            ->map(fn (Model $user) => $user->toArray());
    }

    public function all(int $limit): Collection
    {
        return $this->model
            ->newQuery()
            ->when($limit, function ($query, $limit) {
                $query->take($limit);
            })
            ->get()
            ->map(fn (Model $user) => $user->toArray());
    }

    public function exists(string|int $id): bool
    {
        return $this->model->find($id) !== null;
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->model->find($id)?->delete();
    }

    public function forceDelete(int|string $id): bool
    {
        return $this->delete($id);
    }

    public function getPrimaryKeyName(): string
    {
        return $this->model->getKeyName();
    }
}
