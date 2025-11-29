<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByNameQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByNameQuery $query): ?Country
    {
        return $this->countryRepository->ofName($query->getName());
    }
}
