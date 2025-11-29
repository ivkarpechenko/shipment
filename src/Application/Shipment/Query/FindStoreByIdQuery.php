<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindStoreByIdQuery implements Query
{
    public function __construct(private Uuid $storeId)
    {
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }
}
