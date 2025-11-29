<?php

namespace App\Domain\Currency\Service;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Exception\CurrencyAlreadyCreatedException;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class CreateCurrencyService
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function create(string $code, int $num, string $name): void
    {
        $currency = $this->repository->ofCode($code);

        if (!is_null($currency)) {
            throw new CurrencyAlreadyCreatedException(sprintf('Currency with code %s already created', $code));
        }

        $currency = $this->repository->ofCodeDeactivated($code);
        if (!is_null($currency)) {
            throw new CurrencyDeactivatedException(sprintf('Currency with code %s deactivated', $code));
        }

        $currency = new Currency($code, $num, $name);

        $this->repository->create($currency);
    }
}
