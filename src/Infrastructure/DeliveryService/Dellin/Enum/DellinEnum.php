<?php

namespace App\Infrastructure\DeliveryService\Dellin\Enum;

enum DellinEnum: string
{
    case AUTO = 'auto';
    case EXPRESS = 'express';
    case LETTER = 'letter';
    case AVIA = 'avia';
    case SMALL = 'small';
}
