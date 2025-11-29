<?php

namespace App\Domain\City\Service;

use App\Domain\City\Repository\CityRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class RestoreCityService
{
    public function __construct(public CityRepositoryInterface $repository)
    {
    }

    public function restore(Uuid $cityId): void
    {
        $city = $this->repository->ofIdDeleted($cityId);

        if (is_null($city)) {
            return;
        }

        $city->restored();

        $this->repository->restore($city);
    }
}
