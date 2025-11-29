<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class FindCurrencyByCodeDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCurrencyByCodeDeactivatedQuery $query): ?Currency
    {
        return $this->repository->ofCodeDeactivated($query->getCode());
    }
}
