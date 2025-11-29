<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Command;

use App\Application\Command;

readonly class UpdateDeliveryMethodCommand implements Command
{
    public function __construct(
        public string $code,
        public ?string $name,
        public ?bool $isActive
    ) {
    }
}
