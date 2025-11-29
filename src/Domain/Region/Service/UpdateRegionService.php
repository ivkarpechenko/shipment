<?php

namespace App\Domain\Region\Service;

use App\Domain\Region\Exception\RegionNotFoundException;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateRegionService
{
    public function __construct(public RegionRepositoryInterface $repository)
    {
    }

    public function update(Uuid $regionId, ?string $name, ?bool $isActive): void
    {
        $region = $this->repository->ofId($regionId);

        if (is_null($region)) {
            $region = $this->repository->ofIdDeactivated($regionId);

            if (is_null($region)) {
                throw new RegionNotFoundException(sprintf('Region with ID: %s not found', $regionId->toRfc4122()));
            }
        }

        if (!is_null($isActive) && !$region->equalsIsActive($isActive)) {
            $region->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $region->changeName($name);
        }

        $this->repository->update($region);
    }
}
