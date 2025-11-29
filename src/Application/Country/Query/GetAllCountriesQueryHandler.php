<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;

readonly class GetAllCountriesQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(GetAllCountriesQuery $query): array
    {
        return $this->countryRepository->all();
    }
}
