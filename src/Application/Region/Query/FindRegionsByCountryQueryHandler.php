<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionsByCountryQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionsByCountryQuery $query): array
    {
        return $this->regionRepository->ofCountry($query->getCountry());
    }
}
