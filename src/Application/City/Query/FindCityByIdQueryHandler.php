<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCityByIdQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindCityByIdQuery $query): ?City
    {
        return $this->regionRepository->ofId($query->getCityId());
    }
}
