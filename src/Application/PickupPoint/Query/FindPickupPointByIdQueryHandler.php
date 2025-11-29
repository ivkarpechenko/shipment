<?php

namespace App\Application\PickupPoint\Query;

use App\Application\QueryHandler;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;

readonly class FindPickupPointByIdQueryHandler implements QueryHandler
{
    public function __construct(public PickupPointRepositoryInterface $pickupPointRepository)
    {
    }

    public function __invoke(FindPickupPointByIdQuery $query): ?PickupPoint
    {
        return $this->pickupPointRepository->ofId($query->pickupPointId);
    }
}
