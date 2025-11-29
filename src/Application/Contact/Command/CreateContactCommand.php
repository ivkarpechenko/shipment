<?php

namespace App\Application\Contact\Command;

use App\Application\Command;

readonly class CreateContactCommand implements Command
{
    public function __construct(private string $email, private string $name, private array $phones)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhones(): array
    {
        return $this->phones;
    }
}
