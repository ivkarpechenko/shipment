<?php

namespace App\Domain\Currency\Repository;

use App\Domain\Currency\Entity\Currency;
use Symfony\Component\Uid\Uuid;

interface CurrencyRepositoryInterface
{
    public function create(Currency $currency): void;

    public function update(Currency $currency): void;

    public function ofId(Uuid $currencyId): ?Currency;

    public function ofIdDeactivated(Uuid $currencyId): ?Currency;

    public function ofCode(string $code): ?Currency;

    public function ofCodeDeactivated(string $code): ?Currency;

    public function ofNum(int $num): ?Currency;

    public function ofNumDeactivated(int $num): ?Currency;

    public function all(): array;

    public function paginate(int $page, int $offset): array;
}
