<?php

namespace App\Domain\Currency\Service;

use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class UpdateCurrencyService
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function update(string $code, ?string $name, ?bool $isActive): void
    {
        $currency = $this->repository->ofCode($code);

        if (is_null($currency)) {
            $currency = $this->repository->ofCodeDeactivated($code);
            if (is_null($currency)) {
                throw new CurrencyNotFoundException(sprintf('Currency with code %s not found', $code));
            }
        }

        if (!is_null($isActive) && !$currency->equalsIsActive($isActive)) {
            $currency->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $currency->changeName($name);
        }

        $this->repository->update($currency);
    }
}
