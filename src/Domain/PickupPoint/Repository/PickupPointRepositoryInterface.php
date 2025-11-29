<?php

namespace App\Domain\PickupPoint\Repository;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\PickupPoint\Entity\PickupPoint;
use Symfony\Component\Uid\Uuid;

interface PickupPointRepositoryInterface
{
    public function create(PickupPoint $pickupPoint): void;

    public function update(PickupPoint $pickupPoint): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $pickupPointId): ?PickupPoint;

    public function ofDeliveryServiceAndCode(DeliveryService $deliveryService, string $code): ?PickupPoint;

    public function ofIdDeactivated(Uuid $pickupPointId): ?PickupPoint;
}
