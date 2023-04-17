<?php

namespace Kiedrowski\UserCrudCommand\Traits;

trait HashPasswordFieldTrait
{
    public const DEFAULT_PASSWORD_FIELD = 'password';

    /**
     * @param  array<string>  $inputs
     * @return array<string>
     */
    private function hashPasswordField(array $inputs): array
    {
        if (isset($inputs[self::DEFAULT_PASSWORD_FIELD])) {
            $inputs[self::DEFAULT_PASSWORD_FIELD] = $this->hash->make($inputs[self::DEFAULT_PASSWORD_FIELD]);
        }

        return $inputs;
    }
}
