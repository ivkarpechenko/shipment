<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindProductByStoresQuery implements Query
{
    public function __construct(private array $stores)
    {
    }

    /**
     * @return Uuid[]
     */
    public function getStores(): array
    {
        return $this->stores;
    }
}
