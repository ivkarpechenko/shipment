<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class FindCurrencyByNumQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCurrencyByNumQuery $query): ?Currency
    {
        return $this->repository->ofNum($query->getNum());
    }
}
