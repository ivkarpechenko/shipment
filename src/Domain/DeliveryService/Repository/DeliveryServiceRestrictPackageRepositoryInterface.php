<?php

declare(strict_types=1);

namespace App\Domain\DeliveryService\Repository;

use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use Symfony\Component\Uid\Uuid;

interface DeliveryServiceRestrictPackageRepositoryInterface
{
    public function create(DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage): void;

    public function update(DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage): void;

    public function all(?bool $isActive = null): array;

    public function ofId(Uuid $id): ?DeliveryServiceRestrictPackage;

    public function ofIdDeactivated(Uuid $id): ?DeliveryServiceRestrictPackage;

    public function ofDeliveryServiceId(Uuid $deliveryServiceId): ?DeliveryServiceRestrictPackage;

    public function ofDeliveryServiceIdDeactivated(Uuid $deliveryServiceId): ?DeliveryServiceRestrictPackage;
}
