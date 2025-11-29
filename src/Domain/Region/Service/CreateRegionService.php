<?php

namespace App\Domain\Region\Service;

use App\Domain\Country\Exception\CountryNotFoundException;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Exception\RegionAlreadyCreatedException;
use App\Domain\Region\Exception\RegionDeactivatedException;
use App\Domain\Region\Exception\RegionDeletedException;
use App\Domain\Region\Repository\RegionRepositoryInterface;

readonly class CreateRegionService
{
    public function __construct(
        public RegionRepositoryInterface $repositoryRegion,
        public CountryRepositoryInterface $repositoryCountry
    ) {
    }

    public function create(string $countryCode, string $name, string $code): void
    {
        $country = $this->repositoryCountry->ofCode($countryCode);

        if (is_null($country)) {
            throw new CountryNotFoundException(sprintf('Country with code %s not found', $countryCode));
        }

        $region = $this->repositoryRegion->ofCode($code);
        if (!is_null($region)) {
            throw new RegionAlreadyCreatedException(sprintf('Region with code %s already created', $code));
        }

        $region = $this->repositoryRegion->ofCodeDeactivated($code);
        if (!is_null($region)) {
            throw new RegionDeactivatedException(sprintf('Region with code %s deactivated', $code));
        }

        $region = $this->repositoryRegion->ofCodeDeleted($code);
        if (!is_null($region)) {
            throw new RegionDeletedException(sprintf('Region with code %s deleted', $code));
        }

        $region = new Region($country, $name, $code);

        $this->repositoryRegion->create($region);
    }
}
