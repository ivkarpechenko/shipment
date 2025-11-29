<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\CargoRestriction;
use Symfony\Component\Uid\Uuid;

interface CargoRestrictionRepositoryInterface
{
    public function create(CargoRestriction $restriction): void;

    public function ofId(Uuid $id): ?CargoRestriction;

    public function ofShipmentIdAndCargoTypeCode(Uuid $shipmentId, string $cargoTypeCode): ?CargoRestriction;

    /**
     * @return CargoRestriction[]
     */
    public function ofShipmentId(Uuid $shipmentId): array;

    /**
     * @return CargoRestriction[]
     */
    public function all(): array;
}
