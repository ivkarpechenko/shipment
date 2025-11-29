<?php

namespace App\Domain\DeliveryService\Repository;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use Symfony\Component\Uid\Uuid;

interface DeliveryServiceRestrictAreaRepositoryInterface
{
    public function create(DeliveryServiceRestrictArea $deliveryServiceRestrictArea): DeliveryServiceRestrictArea;

    public function update(DeliveryServiceRestrictArea $deliveryServiceRestrictArea): void;

    public function ofId(Uuid $deliveryServiceRestrictAreaId): ?DeliveryServiceRestrictArea;

    public function ofIdDeactivated(Uuid $deliveryServiceRestrictAreaId): ?DeliveryServiceRestrictArea;

    public function ofDeliveryServiceId(Uuid $deliveryServiceId): array;

    public function ofDeliveryServiceIdDeactivated(Uuid $deliveryServiceId): array;

    public function all(?bool $isActive = null): array;

    public function paginate(int $page, int $offset): array;

    public function ofDeliveryServiceIdAndPoint(Uuid $deliveryServiceId, Point $point): array;
}
