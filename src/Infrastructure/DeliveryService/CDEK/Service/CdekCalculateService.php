<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service;

use App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto\CdekShipmentDto;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekCalculateDto;

class CdekCalculateService
{
    public function __construct(
        public CdekHttpClientService $cdekClient
    ) {
    }

    public function calculate(CdekShipmentDto $dto): CdekCalculateDto
    {
        $response = $this->cdekClient->request(
            'POST',
            'calculator/tariff',
            $this->cdekClient->serializer->normalize($dto)
        );

        return $this->cdekClient->serializer->denormalize($response, CdekCalculateDto::class);
    }
}
