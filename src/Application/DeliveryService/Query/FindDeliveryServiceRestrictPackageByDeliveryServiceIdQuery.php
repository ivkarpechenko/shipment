<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindDeliveryServiceRestrictPackageByDeliveryServiceIdQuery implements Query
{
    public function __construct(
        public Uuid $deliveryServiceId
    ) {
    }
}
