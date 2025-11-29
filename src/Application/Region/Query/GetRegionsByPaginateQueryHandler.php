<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class GetRegionsByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetRegionsByPaginateQuery $query): array
    {
        return $this->regionRepository->paginate($query->getPage(), $query->getOffset());
    }
}
