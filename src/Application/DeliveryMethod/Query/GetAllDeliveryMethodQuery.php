<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\Query;

readonly class GetAllDeliveryMethodQuery implements Query
{
    public function __construct(
        public ?bool $isActive = null
    ) {
    }
}
