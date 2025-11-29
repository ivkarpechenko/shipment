<?php

namespace App\Domain\Shipment\Exception;

class StoreScheduleInvalidDayFormatException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity
}
