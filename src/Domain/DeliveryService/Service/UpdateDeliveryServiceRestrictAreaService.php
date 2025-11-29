<?php

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictAreaDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictAreaNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Symfony\Component\Uid\Uuid;

readonly class UpdateDeliveryServiceRestrictAreaService
{
    public function __construct(
        public DeliveryServiceRestrictAreaRepositoryInterface $deliveryServiceRestrictAreaRepository
    ) {
    }

    public function update(Uuid $deliveryServiceRestrictAreaId, ?string $name = null, ?Polygon $polygon = null, ?bool $isActive = null): void
    {
        $deliveryServiceRestrictArea = $this->deliveryServiceRestrictAreaRepository->ofId($deliveryServiceRestrictAreaId);
        if (is_null($deliveryServiceRestrictArea)) {
            $deliveryServiceRestrictArea = $this->deliveryServiceRestrictAreaRepository->ofIdDeactivated($deliveryServiceRestrictAreaId);
            if (!is_null($deliveryServiceRestrictArea)) {
                throw new DeliveryServiceRestrictAreaDeactivatedException(sprintf(
                    'DeliveryServiceRestrictArea with ID %s deactivated',
                    $deliveryServiceRestrictAreaId->toRfc4122()
                ));
            }

            throw new DeliveryServiceRestrictAreaNotFoundException(
                sprintf('DeliveryServiceRestrictArea with ID %s not found', $deliveryServiceRestrictAreaId->toRfc4122())
            );
        }

        if (!is_null($name)) {
            $deliveryServiceRestrictArea->changeName($name);
        }

        if (!is_null($polygon) && !$deliveryServiceRestrictArea->isEqualPolygon($polygon)) {
            $deliveryServiceRestrictArea->changePolygon($polygon);
        }

        if (!is_null($isActive) && !$deliveryServiceRestrictArea->equalsIsActive($isActive)) {
            $deliveryServiceRestrictArea->changeIsActive($isActive);
        }

        $this->deliveryServiceRestrictAreaRepository->update($deliveryServiceRestrictArea);
    }
}
