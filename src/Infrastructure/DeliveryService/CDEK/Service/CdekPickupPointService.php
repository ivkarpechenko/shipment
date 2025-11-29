<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekPickupPointDto;

class CdekPickupPointService
{
    public function __construct(
        public CdekPickupPointHttpClientService $pvzcdekClient
    ) {
    }

    public function deliveryPoints(): array
    {
        $response = $this->pvzcdekClient->request(
            'GET',
            'deliverypoints?country_code=RU'
        );
        $pickupPoints = [];
        foreach ($response as $pickupPoint) {
            $pickupPoints[] = $this->pvzcdekClient->serializer->denormalize($pickupPoint, CdekPickupPointDto::class);
        }

        return $pickupPoints;
    }
}
