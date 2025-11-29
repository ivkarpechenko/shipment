<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\CargoType;
use Symfony\Component\Uid\Uuid;

interface CargoTypeRepositoryInterface
{
    public function create(CargoType $cargoType): void;

    public function update(CargoType $cargoType): void;

    public function ofId(Uuid $id): ?CargoType;

    public function ofCode(string $code): ?CargoType;

    public function ofCodeDeactivated(string $code): ?CargoType;

    /**
     * @return CargoType[]
     */
    public function all(): array;
}
