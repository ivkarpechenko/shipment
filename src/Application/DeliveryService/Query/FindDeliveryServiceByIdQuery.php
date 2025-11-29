<?php

namespace App\Application\DeliveryService\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindDeliveryServiceByIdQuery implements Query
{
    public function __construct(private Uuid $deliveryServiceId)
    {
    }

    public function getDeliveryServiceId(): Uuid
    {
        return $this->deliveryServiceId;
    }
}
