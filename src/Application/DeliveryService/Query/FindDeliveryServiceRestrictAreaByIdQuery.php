<?php

namespace App\Application\DeliveryService\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindDeliveryServiceRestrictAreaByIdQuery implements Query
{
    public function __construct(private Uuid $deliveryServiceRestrictAreaId)
    {
    }

    public function getDeliveryServiceRestrictAreaId(): Uuid
    {
        return $this->deliveryServiceRestrictAreaId;
    }
}
