<?php

namespace App\Application\PickupPoint\Query;

use App\Application\QueryHandler;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;

readonly class GetAllPickupPointsQueryHandler implements QueryHandler
{
    public function __construct(public PickupPointRepositoryInterface $pickupPointRepository)
    {
    }

    public function __invoke(GetAllPickupPointsQuery $query): array
    {
        return $this->pickupPointRepository->all();
    }
}
