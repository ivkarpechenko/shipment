<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto;

class CdekErrorDto
{
    public function __construct(
        public string $code,
        public string $message,
    ) {
    }
}
