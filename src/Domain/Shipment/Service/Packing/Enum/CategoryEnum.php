<?php

namespace App\Domain\Shipment\Service\Packing\Enum;

enum CategoryEnum: string
{
    case REGULAR = 'regular';
    case FRAGILE = 'fragile';
    case FLAMMABLE = 'flammable';
    case LARGE_OR_HEAVY = 'largeOrHeavy';
}
