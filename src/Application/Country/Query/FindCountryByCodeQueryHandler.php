<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByCodeQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByCodeQuery $query): ?Country
    {
        return $this->countryRepository->ofCode($query->getCode());
    }
}
