<?php

declare(strict_types=1);

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateDeliveryServiceRestrictPackageService
{
    public function __construct(
        public DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepository
    ) {
    }

    public function update(Uuid $id, int $maxWeight, int $maxWidth, int $maxHeight, int $maxLength, bool $isActive): void
    {
        $deliveryServiceRestrictPackage = $this->deliveryServiceRestrictPackageRepository->ofId($id);
        if (is_null($deliveryServiceRestrictPackage)) {
            $deliveryServiceRestrictPackage = $this->deliveryServiceRestrictPackageRepository->ofIdDeactivated($id);
            if (is_null($deliveryServiceRestrictPackage)) {
                throw new DeliveryServiceRestrictPackageNotFoundException(
                    sprintf('Delivery service restrict package with id "%s" not found.', $id->toRfc4122())
                );
            }
        }

        $deliveryServiceRestrictPackage->change($maxWeight, $maxWidth, $maxHeight, $maxLength, $isActive);

        $this->deliveryServiceRestrictPackageRepository->update($deliveryServiceRestrictPackage);
    }
}
