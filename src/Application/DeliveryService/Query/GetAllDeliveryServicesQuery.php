<?php

namespace App\Application\DeliveryService\Query;

use App\Application\Query;

readonly class GetAllDeliveryServicesQuery implements Query
{
    public function __construct(private ?bool $isActive = null)
    {
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
}
