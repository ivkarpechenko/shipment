<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\ProductDto;

readonly class CreateStoreWithProductsCommand implements Command
{
    public function __construct(private array $products)
    {
    }

    /**
     * @return ProductDto[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}
