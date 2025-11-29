<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionByIdQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionByIdQuery $query): ?Region
    {
        return $this->regionRepository->ofId($query->getRegionId());
    }
}
