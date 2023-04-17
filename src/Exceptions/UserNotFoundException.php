<?php

namespace Kiedrowski\UserCrudCommand\Exceptions;

class UserNotFoundException extends \Exception
{
    private const MESSAGE = 'User with %s %s not found.';

    private const CODE = 404;

    public function __construct(string $idColumnName, string|int $id)
    {
        parent::__construct(sprintf(self::MESSAGE, $idColumnName, $id), self::CODE);
    }
}
