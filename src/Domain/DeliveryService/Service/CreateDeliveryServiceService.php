<?php

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Exception\DeliveryServiceAlreadyCreatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class CreateDeliveryServiceService
{
    public function __construct(public DeliveryServiceRepositoryInterface $repository)
    {
    }

    public function create(string $code, string $name): void
    {
        $deliveryService = $this->repository->ofCode($code);
        if (!is_null($deliveryService)) {
            throw new DeliveryServiceAlreadyCreatedException(sprintf('Delivery service with code %s already created', $code));
        }

        $deliveryService = $this->repository->ofCodeDeactivated($code);
        if (!is_null($deliveryService)) {
            throw new DeliveryServiceDeactivatedException(sprintf('Delivery service with code %s deactivated', $code));
        }

        $deliveryService = new DeliveryService($code, $name);

        $this->repository->create($deliveryService);
    }
}
