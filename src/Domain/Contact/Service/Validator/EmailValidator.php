<?php

namespace App\Domain\Contact\Service\Validator;

class EmailValidator
{
    private const REGEX = '/^\S+@\S+\.\S+$/';

    public function validate(string $email): void
    {
        if (!$this->isValid($email)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Email %s invalid format, email must match regex %s',
                    $email,
                    self::REGEX
                )
            );
        }
    }

    protected function isValid(string $email): bool
    {
        return preg_match(self::REGEX, $email);
    }
}
