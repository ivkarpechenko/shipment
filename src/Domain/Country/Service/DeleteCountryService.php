<?php

namespace App\Domain\Country\Service;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class DeleteCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function delete(Uuid $countryId): void
    {
        $country = $this->repository->ofId($countryId);

        if (is_null($country)) {
            return;
        }

        $country->deleted();

        $this->repository->delete($country);
    }
}
