<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\StoreDto;

readonly class CreateStoreCommand implements Command
{
    public function __construct(private StoreDto $storeDto, private array $products)
    {
    }

    public function getStoreDto(): StoreDto
    {
        return $this->storeDto;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
