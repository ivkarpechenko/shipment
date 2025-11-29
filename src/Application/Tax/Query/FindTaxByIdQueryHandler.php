<?php

namespace App\Application\Tax\Query;

use App\Application\QueryHandler;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;

readonly class FindTaxByIdQueryHandler implements QueryHandler
{
    public function __construct(public TaxRepositoryInterface $taxRepository)
    {
    }

    public function __invoke(FindTaxByIdQuery $query): ?Tax
    {
        return $this->taxRepository->ofId($query->getTaxId());
    }
}
