<?php

namespace App\Domain\Country\Service;

use App\Domain\Country\Exception\CountryNotFoundException;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function update(Uuid $countryId, ?string $name, ?bool $isActive): void
    {
        $country = $this->repository->ofId($countryId);

        if (is_null($country)) {
            $country = $this->repository->ofIdDeactivated($countryId);

            if (is_null($country)) {
                throw new CountryNotFoundException(sprintf('Country with ID: %s not found', $countryId->toRfc4122()));
            }
        }

        if (!is_null($isActive) && !$country->equalsIsActive($isActive)) {
            $country->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $country->changeName($name);
        }

        $this->repository->update($country);
    }
}
