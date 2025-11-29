<?php

namespace App\Domain\Shipment\Service\Packing\Factory;

use App\Domain\Shipment\Service\Packing\Box;
use App\Domain\Shipment\Service\Packing\Strategy\FlammablePackingStrategy;

readonly class FlammableBoxFactory implements BoxFactoryInterface
{
    public function createBox(): Box
    {
        return new Box(new FlammablePackingStrategy());
    }
}
