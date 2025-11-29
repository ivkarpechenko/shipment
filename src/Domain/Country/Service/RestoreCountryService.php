<?php

namespace App\Domain\Country\Service;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class RestoreCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function restore(Uuid $countryId): void
    {
        $country = $this->repository->ofIdDeleted($countryId);

        if (is_null($country)) {
            return;
        }

        $country->restored();

        $this->repository->restore($country);
    }
}
