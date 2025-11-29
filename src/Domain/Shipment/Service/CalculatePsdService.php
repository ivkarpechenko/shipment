<?php

namespace App\Domain\Shipment\Service;

class CalculatePsdService
{
    // TODO: implement PSD generator
    public function calculate($store): array
    {
        return [date('Y-m-d'), date('H:i'), date('H:i')];
    }
}
