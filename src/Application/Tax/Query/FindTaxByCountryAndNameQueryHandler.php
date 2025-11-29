<?php

namespace App\Application\Tax\Query;

use App\Application\QueryHandler;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;

readonly class FindTaxByCountryAndNameQueryHandler implements QueryHandler
{
    public function __construct(public TaxRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindTaxByCountryAndNameQuery $query): ?Tax
    {
        return $this->regionRepository->ofCountryAndName($query->getCountry(), $query->getName());
    }
}
