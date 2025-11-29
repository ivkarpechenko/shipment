<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCityByTypeAndNameQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindCityByTypeAndNameQuery $query): ?City
    {
        return $this->regionRepository->ofTypeAndName($query->getType(), $query->getName());
    }
}
