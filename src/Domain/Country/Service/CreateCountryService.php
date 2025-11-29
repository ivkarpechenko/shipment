<?php

namespace App\Domain\Country\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Exception\CountryAlreadyCreatedException;
use App\Domain\Country\Exception\CountryDeactivatedException;
use App\Domain\Country\Exception\CountryDeletedException;
use App\Domain\Country\Repository\CountryRepositoryInterface;

readonly class CreateCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function create(string $name, string $code): void
    {
        $country = $this->repository->ofCode($code);
        if (!is_null($country)) {
            throw new CountryAlreadyCreatedException(sprintf('Country with code %s already created', $code));
        }

        $country = $this->repository->ofCodeDeactivated($code);
        if (!is_null($country)) {
            throw new CountryDeactivatedException(sprintf('Country with code %s deactivated', $code));
        }

        $country = $this->repository->ofCodeDeleted($code);
        if (!is_null($country)) {
            throw new CountryDeletedException(sprintf('Country with code %s deleted', $code));
        }

        $country = new Country($name, $code);

        $this->repository->create($country);
    }
}
