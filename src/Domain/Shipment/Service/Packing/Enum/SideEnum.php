<?php

namespace App\Domain\Shipment\Service\Packing\Enum;

enum SideEnum: string
{
    case WIDTH = 'width';
    case LENGTH = 'length';
    case HEIGHT = 'height';
}
