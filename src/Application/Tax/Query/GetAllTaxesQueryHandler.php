<?php

namespace App\Application\Tax\Query;

use App\Application\QueryHandler;
use App\Domain\Tax\Repository\TaxRepositoryInterface;

readonly class GetAllTaxesQueryHandler implements QueryHandler
{
    public function __construct(public TaxRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetAllTaxesQuery $query): array
    {
        return $this->regionRepository->all();
    }
}
