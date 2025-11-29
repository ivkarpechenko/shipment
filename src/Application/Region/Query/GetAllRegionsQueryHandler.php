<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class GetAllRegionsQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetAllRegionsQuery $query): array
    {
        return $this->regionRepository->all();
    }
}
