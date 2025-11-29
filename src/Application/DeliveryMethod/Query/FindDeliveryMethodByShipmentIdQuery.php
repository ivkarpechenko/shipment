<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindDeliveryMethodByShipmentIdQuery implements Query
{
    public function __construct(
        public Uuid $shipmentId
    ) {
    }
}
