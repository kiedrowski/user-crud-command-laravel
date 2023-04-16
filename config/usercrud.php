<?php

use Kiedrowski\UserCrudCommand\Commands\CreateCommand;
use Kiedrowski\UserCrudCommand\Repository\UserRepositoryInterface;

return [
    'model_class' => UserRepositoryInterface::DEFAULT_USER_MODEL_CLASS,
    'required_fields' => CreateCommand::DEFAULT_REQUIRED_FIELDS,
    'password_field' => CreateCommand::DEFAULT_PASSWORD_FIELD,
];
