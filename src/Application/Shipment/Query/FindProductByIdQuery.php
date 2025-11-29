<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindProductByIdQuery implements Query
{
    public function __construct(private Uuid $productId)
    {
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }
}
