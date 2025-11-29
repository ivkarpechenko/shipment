<?php

namespace App\Domain\City\Repository;

use App\Domain\City\Entity\City;
use App\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

interface CityRepositoryInterface
{
    public function create(City $city): void;

    public function update(City $city): void;

    public function delete(City $city): void;

    public function restore(City $city): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $cityId): ?City;

    public function ofRegion(Region $region): array;

    public function ofIdDeleted(Uuid $cityId): ?City;

    public function ofIdDeactivated(Uuid $cityId): ?City;

    public function ofTypeAndName(string $type, string $name): ?City;

    public function ofTypeAndNameDeleted(string $type, string $name): ?City;

    public function ofTypeAndNameDeactivated(string $type, string $name): ?City;

    public function ofType(string $type): array;
}
