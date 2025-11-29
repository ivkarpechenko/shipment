<?php

namespace App\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CityAlreadyCreatedException;
use App\Domain\City\Exception\CityDeactivatedException;
use App\Domain\City\Exception\CityDeletedException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Exception\CountryNotFoundException;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class CreateCityService
{
    public function __construct(
        public CityRepositoryInterface $repositoryCity,
        public RegionRepositoryInterface $repositoryRegion
    ) {
    }

    public function create(string $regionCode, string $type, string $name): void
    {
        $region = $this->repositoryRegion->ofCode($regionCode);

        if (is_null($region)) {
            throw new CountryNotFoundException(sprintf('Region with code: %s not found', $regionCode));
        }

        $city = $this->repositoryCity->ofTypeAndName($type, $name);
        if (!is_null($city)) {
            throw new CityAlreadyCreatedException(sprintf('City with type %s and name %s already created', $type, $name));
        }

        $city = $this->repositoryCity->ofTypeAndNameDeactivated($type, $name);
        if (!is_null($city)) {
            throw new CityDeactivatedException(sprintf('City with type %s and name %s deactivated', $type, $name));
        }

        $city = $this->repositoryCity->ofTypeAndNameDeleted($type, $name);
        if (!is_null($city)) {
            throw new CityDeletedException(sprintf('City with type %s and name %s deleted', $type, $name));
        }

        $city = new City($region, $type, $name);

        $this->repositoryCity->create($city);
    }
}
