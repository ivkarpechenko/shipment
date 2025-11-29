<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Command;

use App\Application\Command;

readonly class CreateDeliveryMethodCommand implements Command
{
    public function __construct(
        public string $code,
        public string $name
    ) {
    }
}
