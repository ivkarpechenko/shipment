<?php

namespace App\Domain\Shipment\Service\Packing\Factory;

use App\Domain\Shipment\Service\Packing\Box;
use App\Domain\Shipment\Service\Packing\Strategy\FragilePackingStrategy;

readonly class FragileBoxFactory implements BoxFactoryInterface
{
    public function createBox(): Box
    {
        return new Box(new FragilePackingStrategy());
    }
}
