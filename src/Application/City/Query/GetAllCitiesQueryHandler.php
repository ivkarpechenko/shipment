<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class GetAllCitiesQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetAllCitiesQuery $query): array
    {
        return $this->regionRepository->all();
    }
}
