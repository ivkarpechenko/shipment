<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class GetCitiesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetCitiesByPaginateQuery $query): array
    {
        return $this->regionRepository->paginate($query->getPage(), $query->getOffset());
    }
}
