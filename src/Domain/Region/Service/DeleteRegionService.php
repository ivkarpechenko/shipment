<?php

namespace App\Domain\Region\Service;

use App\Domain\Region\Repository\RegionRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class DeleteRegionService
{
    public function __construct(public RegionRepositoryInterface $repository)
    {
    }

    public function delete(Uuid $regionId): void
    {
        $region = $this->repository->ofId($regionId);

        if (is_null($region)) {
            return;
        }

        $region->deleted();

        $this->repository->delete($region);
    }
}
