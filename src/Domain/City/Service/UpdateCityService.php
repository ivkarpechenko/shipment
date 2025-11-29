<?php

namespace App\Domain\City\Service;

use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCityService
{
    public function __construct(public CityRepositoryInterface $repository)
    {
    }

    public function update(Uuid $cityId, ?string $type, ?string $name, ?bool $isActive): void
    {
        $city = $this->repository->ofId($cityId);

        if (is_null($city)) {
            $city = $this->repository->ofIdDeactivated($cityId);

            if (is_null($city)) {
                throw new CityNotFoundException(sprintf('City with ID: %s not found', $cityId->toRfc4122()));
            }
        }

        if (!is_null($isActive) && !$city->equalsIsActive($isActive)) {
            $city->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $city->changeName($name);
        }

        if (!is_null($type)) {
            $city->changeType($type);
        }

        $this->repository->update($city);
    }
}
