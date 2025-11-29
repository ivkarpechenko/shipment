<?php

namespace App\Domain\PickupPoint\Service;

use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;

readonly class ChangePickupPointService
{
    public function __construct(
        public PickupPointRepositoryInterface $repository,
        public CreatePickupPointService $createPickupPointService
    ) {
    }

    public function change(PickupPointDto $dto): void
    {
        $pickupPoint = $this->repository->ofDeliveryServiceAndCode($dto->deliveryService, $dto->code);
        if (is_null($pickupPoint)) {
            $this->createPickupPointService->create($dto);

            return;
        }

        $pickupPoint->change($dto);

        $this->repository->update($pickupPoint);
    }
}
