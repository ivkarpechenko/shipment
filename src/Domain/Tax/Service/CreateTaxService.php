<?php

namespace App\Domain\Tax\Service;

use App\Domain\Country\Exception\CountryNotFoundException;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Exception\TaxAlreadyCreatedException;
use App\Domain\Tax\Exception\TaxDeletedException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;

readonly class CreateTaxService
{
    public function __construct(
        public TaxRepositoryInterface $taxRepository,
        public CountryRepositoryInterface $countryRepository
    ) {
    }

    public function create(string $countryCode, string $name, float $value, string $expression): void
    {
        $country = $this->countryRepository->ofCode($countryCode);

        if (is_null($country)) {
            throw new CountryNotFoundException(sprintf('Country with code %s not found', $countryCode));
        }

        $tax = $this->taxRepository->ofCountryAndName($country, $name);
        if (!is_null($tax)) {
            throw new TaxAlreadyCreatedException(sprintf('Tax with country %s and name %s already created', $country->getName(), $name));
        }

        $tax = $this->taxRepository->ofCountryAndNameDeleted($country, $name);
        if (!is_null($tax)) {
            throw new TaxDeletedException(sprintf('Tax with country %s and name %s deleted', $country->getName(), $name));
        }

        $tax = new Tax($country, $name, $value, $expression);

        $this->taxRepository->create($tax);
    }
}
