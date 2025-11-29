<?php

namespace App\Application\Currency\Command;

use App\Application\Command;

readonly class CreateCurrencyCommand implements Command
{
    public function __construct(private string $code, private int $num, private string $name)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
