<?php

namespace App\Infrastructure\DaData\Exception;

use Exception;
use Throwable;

class DaDataException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
