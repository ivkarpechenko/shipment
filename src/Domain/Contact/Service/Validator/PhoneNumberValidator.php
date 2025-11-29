<?php

namespace App\Domain\Contact\Service\Validator;

class PhoneNumberValidator
{
    private const REGEX = '/^\+[1-9][0-9]{7,14}$/';

    public function validate(string $number): void
    {
        if (!$this->isValid($number)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Phone number %s invalid format, number must match regex %s',
                    $number,
                    self::REGEX
                )
            );
        }
    }

    protected function isValid(string $number): bool
    {
        return preg_match(self::REGEX, $number);
    }
}
