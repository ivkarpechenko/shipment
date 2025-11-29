<?php

namespace App\Domain\Shipment\Service\Packing\Factory;

use App\Domain\Shipment\Service\Packing\Box;

interface BoxFactoryInterface
{
    public function createBox(): Box;
}
