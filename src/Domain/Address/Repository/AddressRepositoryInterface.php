<?php

namespace App\Domain\Address\Repository;

use App\Domain\Address\Entity\Address;
use App\Domain\City\Entity\City;
use Symfony\Component\Uid\Uuid;

interface AddressRepositoryInterface
{
    public function create(Address $address): void;

    public function update(Address $address): void;

    public function delete(Address $address): void;

    public function restore(Address $address): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $addressId): ?Address;

    public function ofIdDeleted(Uuid $addressId): ?Address;

    public function ofIdDeactivated(Uuid $addressId): ?Address;

    public function ofCity(City $city): array;

    public function ofAddress(string $address): ?Address;

    public function ofAddressDeleted(string $address): ?Address;

    public function ofAddressDeactivated(string $address): ?Address;
}
