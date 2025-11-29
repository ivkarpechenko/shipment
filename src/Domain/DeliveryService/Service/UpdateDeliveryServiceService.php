<?php

namespace App\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class UpdateDeliveryServiceService
{
    public function __construct(public DeliveryServiceRepositoryInterface $repository)
    {
    }

    public function update(string $code, ?string $name, ?bool $isActive): void
    {
        $deliveryService = $this->repository->ofCode($code);
        if (is_null($deliveryService)) {
            $deliveryService = $this->repository->ofCodeDeactivated($code);
            if (is_null($deliveryService)) {
                throw new DeliveryServiceNotFoundException(sprintf('Delivery service with code %s not found', $code));
            }
        }

        if (!is_null($isActive) && !$deliveryService->equalsIsActive($isActive)) {
            $deliveryService->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $deliveryService->changeName($name);
        }

        $this->repository->update($deliveryService);
    }
}
