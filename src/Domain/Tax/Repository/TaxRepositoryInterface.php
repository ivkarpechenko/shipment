<?php

namespace App\Domain\Tax\Repository;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Entity\Tax;
use Symfony\Component\Uid\Uuid;

interface TaxRepositoryInterface
{
    public function create(Tax $tax): void;

    public function update(Tax $tax): void;

    public function delete(Tax $tax): void;

    public function restore(Tax $tax): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $taxId): ?Tax;

    public function ofIdDeleted(Uuid $taxId): ?Tax;

    public function ofCountry(Country $country): array;

    public function ofCountryAndName(Country $country, string $name): ?Tax;

    public function ofCountryAndNameDeleted(Country $country, string $name): ?Tax;
}
