<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindProductByStoreAndDeliveryPeriodQuery implements Query
{
    public function __construct(private Uuid $storeId, private int $deliveryPeriod)
    {
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getDeliveryPeriod(): int
    {
        return $this->deliveryPeriod;
    }
}
