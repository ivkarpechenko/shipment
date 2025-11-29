<?php

namespace App\Domain\City\Service;

use App\Domain\City\Repository\CityRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class DeleteCityService
{
    public function __construct(public CityRepositoryInterface $repository)
    {
    }

    public function delete(Uuid $cityId): void
    {
        $city = $this->repository->ofId($cityId);

        if (is_null($city)) {
            return;
        }

        $city->deleted();

        $this->repository->delete($city);
    }
}
