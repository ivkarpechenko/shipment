<?php

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\Store;
use Symfony\Component\Uid\Uuid;

interface StoreRepositoryInterface
{
    public function create(Store $store): Uuid;

    public function ofId(Uuid $storeId): ?Store;

    public function ofExternalId(int $externalId): ?Store;

    public function ofIdAndExternalId(Uuid $storeId, int $externalId): ?Store;
}
