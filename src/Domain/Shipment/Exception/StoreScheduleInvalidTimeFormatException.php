<?php

namespace App\Domain\Shipment\Exception;

class StoreScheduleInvalidTimeFormatException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity
}
