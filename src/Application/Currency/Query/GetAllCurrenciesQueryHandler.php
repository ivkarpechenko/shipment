<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class GetAllCurrenciesQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(GetAllCurrenciesQuery $query): array
    {
        return $this->repository->all();
    }
}
