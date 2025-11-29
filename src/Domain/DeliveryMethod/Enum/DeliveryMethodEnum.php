<?php

namespace App\Domain\DeliveryMethod\Enum;

enum DeliveryMethodEnum: string
{
    case COURIER = 'courier';
    case PVZ = 'pvz';
    case PICKUP = 'pickup';
}
