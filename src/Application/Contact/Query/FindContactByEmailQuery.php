<?php

namespace App\Application\Contact\Query;

use App\Application\Query;

readonly class FindContactByEmailQuery implements Query
{
    public function __construct(private string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
