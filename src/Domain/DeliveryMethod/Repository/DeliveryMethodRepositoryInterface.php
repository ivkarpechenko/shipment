<?php

declare(strict_types=1);

namespace App\Domain\DeliveryMethod\Repository;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use Symfony\Component\Uid\Uuid;

interface DeliveryMethodRepositoryInterface
{
    public function create(DeliveryMethod $deliveryMethod): void;

    public function update(DeliveryMethod $deliveryMethod): void;

    public function ofCode(string $code): ?DeliveryMethod;

    public function ofCodeDeactivated(string $code): ?DeliveryMethod;

    public function ofId(Uuid $id): ?DeliveryMethod;

    public function all(?bool $isActive = null): array;
}
