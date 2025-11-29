<?php

declare(strict_types=1);

namespace App\Application\Directory\Command;

use App\Application\Command;
use App\Domain\Directory\Dto\OkatoOktmoDto;

readonly class CreateOkatoOktmoCommand implements Command
{
    public function __construct(public OkatoOktmoDto $dto)
    {
    }
}
