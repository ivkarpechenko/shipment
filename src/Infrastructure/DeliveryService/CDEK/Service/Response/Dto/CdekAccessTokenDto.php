<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto;

class CdekAccessTokenDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public string $scope,
        public string $jti,
    ) {
    }
}
