<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\Query;

readonly class FindDeliveryMethodByCodeQuery implements Query
{
    public function __construct(
        public string $code
    ) {
    }
}
