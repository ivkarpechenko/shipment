<?php

namespace App\Domain\Region\Repository;

use App\Domain\Country\Entity\Country;
use App\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

interface RegionRepositoryInterface
{
    public function create(Region $region): void;

    public function update(Region $region): void;

    public function delete(Region $region): void;

    public function restore(Region $region): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $regionId): ?Region;

    public function ofCountry(Country $country): array;

    public function ofIdDeleted(Uuid $regionId): ?Region;

    public function ofIdDeactivated(Uuid $regionId): ?Region;

    public function ofCode(string $code): ?Region;

    public function ofCodeDeleted(string $code): ?Region;

    public function ofCodeDeactivated(string $code): ?Region;

    public function ofName(string $name): ?Region;
}
