<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service;

use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinShipmentDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinCalculateDto;
use Symfony\Component\Serializer\SerializerInterface;

class DellinCalculateService
{
    public function __construct(
        public DellinHttpClientService $dellinHttpClientService,
        public SerializerInterface $serializer
    ) {
    }

    public function calculate(DellinShipmentDto $dellinShipmentDto): DellinCalculateDto
    {
        $calculateResponse = $this->dellinHttpClientService->request(
            'POST',
            'v2/calculator.json',
            $this->serializer->normalize($dellinShipmentDto, DellinShipmentDto::class)
        );

        return $this->dellinHttpClientService
            ->serializer
            ->denormalize(
                $calculateResponse,
                DellinCalculateDto::class,
                context: [
                    'tariffPlanCode' => $dellinShipmentDto->tariffPlan->getCode(),
                    'pickupPoint' => $dellinShipmentDto->pickupPoint,
                ]
            );
    }
}
