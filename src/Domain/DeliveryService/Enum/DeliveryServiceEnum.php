<?php

namespace App\Domain\DeliveryService\Enum;

enum DeliveryServiceEnum: string
{
    case DELLIN = 'dellin';
    case CDEK = 'cdek';
    case DOSTAVISTA = 'dostavista';
    case PICKUP = 'pickup';
}
