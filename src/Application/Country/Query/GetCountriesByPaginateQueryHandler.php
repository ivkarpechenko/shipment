<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;

readonly class GetCountriesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(GetCountriesByPaginateQuery $query): array
    {
        return $this->countryRepository->paginate($query->getPage(), $query->getOffset());
    }
}
