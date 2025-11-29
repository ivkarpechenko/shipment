<?php

namespace App\Domain\Region\Service;

use App\Domain\Region\Repository\RegionRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class RestoreRegionService
{
    public function __construct(public RegionRepositoryInterface $repository)
    {
    }

    public function restore(Uuid $regionId): void
    {
        $region = $this->repository->ofIdDeleted($regionId);

        if (is_null($region)) {
            return;
        }

        $region->restored();

        $this->repository->restore($region);
    }
}
