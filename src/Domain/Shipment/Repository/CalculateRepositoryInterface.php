<?php

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\Calculate;
use Symfony\Component\Uid\Uuid;

interface CalculateRepositoryInterface
{
    public function create(Calculate $calculate): Uuid;

    public function update(Calculate $calculate): Uuid;

    public function paginate(int $page, int $offset): array;

    public function ofIdNotExpired(Uuid $calculateId): ?Calculate;

    public function ofShipmentIdNotExpired(Uuid $shipmentId): array;

    public function ofShipmentAndTariffPlanIdNotExpired(Uuid $shipmentId, Uuid $tariffPlanId): ?Calculate;
}
