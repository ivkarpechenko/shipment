<?php

namespace App\Domain\PickupPoint\Service;

use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;

readonly class CreatePickupPointService
{
    public function __construct(
        public PickupPointRepositoryInterface $repository
    ) {
    }

    public function create(PickupPointDto $dto): void
    {
        $pickupPoint = new PickupPoint(
            deliveryService: $dto->deliveryService,
            phones: $dto->phones,
            point: $dto->point,
            address: $dto->address,
            workTime: $dto->workTime,
            name: $dto->name,
            code: $dto->code,
            type: $dto->type,
            weightMin: $dto->weightMin,
            weightMax: $dto->weightMax,
            width: $dto->width,
            height: $dto->height,
            depth: $dto->depth,
        );

        $this->repository->create($pickupPoint);
    }
}
