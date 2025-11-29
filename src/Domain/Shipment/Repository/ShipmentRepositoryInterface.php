<?php

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\Shipment;
use Symfony\Component\Uid\Uuid;

interface ShipmentRepositoryInterface
{
    public function create(Shipment $shipment): Uuid;

    public function update(Shipment $shipment): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $shipmentId): ?Shipment;

    public function ofShipments(array $shipments): array;
}
