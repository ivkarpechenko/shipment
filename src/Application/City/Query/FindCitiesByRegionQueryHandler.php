<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCitiesByRegionQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindCitiesByRegionQuery $query): array
    {
        return $this->regionRepository->ofRegion($query->getRegion());
    }
}
