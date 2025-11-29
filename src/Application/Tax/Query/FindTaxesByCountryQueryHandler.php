<?php

namespace App\Application\Tax\Query;

use App\Application\QueryHandler;
use App\Domain\Tax\Repository\TaxRepositoryInterface;

readonly class FindTaxesByCountryQueryHandler implements QueryHandler
{
    public function __construct(public TaxRepositoryInterface $taxRepository)
    {
    }

    public function __invoke(FindTaxesByCountryQuery $query): array
    {
        return $this->taxRepository->ofCountry($query->getCountry());
    }
}
