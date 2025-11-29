<?php

declare(strict_types=1);

namespace App\Application\Directory\Command;

use App\Application\Command;

readonly class ImportOkatoOktmoCommand implements Command
{
    public function __construct(
        public string $filepath
    ) {
    }
}
