<?php

namespace App\Domain\DeliveryService\Repository;

use App\Domain\DeliveryService\Entity\DeliveryService;
use Symfony\Component\Uid\Uuid;

interface DeliveryServiceRepositoryInterface
{
    public function create(DeliveryService $deliveryService): void;

    public function update(DeliveryService $deliveryService): void;

    public function ofId(Uuid $deliveryServiceId): ?DeliveryService;

    public function ofIdDeactivated(Uuid $deliveryServiceId): ?DeliveryService;

    public function ofCode(string $code): ?DeliveryService;

    public function ofCodeDeactivated(string $code): ?DeliveryService;

    public function all(?bool $isActive = null): array;

    public function paginate(int $page, int $offset): array;
}
