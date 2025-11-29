<?php

namespace App\Domain\Shipment\Enum;

enum CargoTypeEnum: string
{
    case SMALL_SIZED = 'small_sized'; // малогабаритный груз
    case LARGE_SIZED = 'large_sized'; // крупногабаритный груз
}
