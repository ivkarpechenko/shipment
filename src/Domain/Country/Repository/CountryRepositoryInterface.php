<?php

namespace App\Domain\Country\Repository;

use App\Domain\Country\Entity\Country;
use Symfony\Component\Uid\Uuid;

interface CountryRepositoryInterface
{
    public function create(Country $country): void;

    public function update(Country $country): void;

    public function delete(Country $country): void;

    public function restore(Country $country): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $countryId): ?Country;

    public function ofIdDeleted(Uuid $countryId): ?Country;

    public function ofIdDeactivated(Uuid $countryId): ?Country;

    public function ofCode(string $code): ?Country;

    public function ofCodeDeleted(string $code): ?Country;

    public function ofCodeDeactivated(string $code): ?Country;

    public function ofName(string $name): ?Country;
}
