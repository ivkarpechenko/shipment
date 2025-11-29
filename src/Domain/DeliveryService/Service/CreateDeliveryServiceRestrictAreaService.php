<?php

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Symfony\Component\Uid\Uuid;

readonly class CreateDeliveryServiceRestrictAreaService
{
    public function __construct(
        public DeliveryServiceRestrictAreaRepositoryInterface $deliveryServiceRestrictAreaRepository,
        public DeliveryServiceRepositoryInterface $deliveryServiceRepository
    ) {
    }

    public function create(Uuid $deliveryServiceId, string $name, Polygon $polygon): DeliveryServiceRestrictArea
    {
        $deliveryService = $this->deliveryServiceRepository->ofId($deliveryServiceId);
        if (is_null($deliveryService)) {
            $deliveryService = $this->deliveryServiceRepository->ofIdDeactivated($deliveryServiceId);

            if (is_null($deliveryService)) {
                throw new DeliveryServiceNotFoundException(sprintf(
                    'Delivery service with ID: %s not found',
                    $deliveryServiceId->toRfc4122()
                ));
            }

            throw new DeliveryServiceDeactivatedException(
                sprintf(
                    'Delivery service with ID %s deactivated',
                    $deliveryServiceId->toRfc4122()
                )
            );
        }

        $deliveryServiceRestrictArea = new DeliveryServiceRestrictArea(
            $deliveryService,
            $name,
            $polygon
        );

        return $this->deliveryServiceRestrictAreaRepository->create($deliveryServiceRestrictArea);
    }
}
