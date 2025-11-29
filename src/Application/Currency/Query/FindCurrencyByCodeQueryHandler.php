<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class FindCurrencyByCodeQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCurrencyByCodeQuery $query): ?Currency
    {
        return $this->repository->ofCode($query->getCode());
    }
}
