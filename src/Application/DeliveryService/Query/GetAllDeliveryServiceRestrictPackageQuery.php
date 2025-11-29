<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Query;

use App\Application\Query;

readonly class GetAllDeliveryServiceRestrictPackageQuery implements Query
{
    public function __construct(
        public ?bool $isActive = null,
    ) {
    }
}
