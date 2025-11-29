<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;

class FindCountryByIdQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByIdQuery $query): ?Country
    {
        return $this->countryRepository->ofId($query->getCountryId());
    }
}
