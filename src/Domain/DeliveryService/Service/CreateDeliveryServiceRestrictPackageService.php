<?php

declare(strict_types=1);

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageAlreadyCreatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageDeactivatedException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateDeliveryServiceRestrictPackageService
{
    public function __construct(
        public DeliveryServiceRepositoryInterface $deliveryServiceRepository,
        public DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepository
    ) {
    }

    public function create(Uuid $deliveryServiceId, int $maxWeight, int $maxWidth, int $maxLength, int $maxHeight): void
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

        $deliveryServiceRestrictPackage = $this->deliveryServiceRestrictPackageRepository->ofDeliveryServiceId($deliveryService->getId());
        if (!is_null($deliveryServiceRestrictPackage)) {
            throw new DeliveryServiceRestrictPackageAlreadyCreatedException(
                sprintf(
                    'Delivery service restrict package for delivery service %s already created',
                    $deliveryService->getName()
                )
            );
        }

        $deliveryServiceRestrictPackage = $this->deliveryServiceRestrictPackageRepository->ofDeliveryServiceIdDeactivated($deliveryService->getId());
        if (!is_null($deliveryServiceRestrictPackage)) {
            throw new DeliveryServiceRestrictPackageDeactivatedException(
                sprintf(
                    'Delivery service restrict package for delivery service %s deactivated',
                    $deliveryService->getName()
                )
            );
        }

        $deliveryServiceRestrictPackage = new DeliveryServiceRestrictPackage(
            deliveryService: $deliveryService,
            maxWeight: $maxWeight,
            maxWidth: $maxWidth,
            maxHeight: $maxHeight,
            maxLength: $maxLength
        );

        $this->deliveryServiceRestrictPackageRepository->create($deliveryServiceRestrictPackage);
    }
}
