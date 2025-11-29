<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;

readonly class GetAllDeliveryServiceRestrictPackageQueryHandler implements QueryHandler
{
    public function __construct(
        public DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepository
    ) {
    }

    public function __invoke(GetAllDeliveryServiceRestrictPackageQuery $query): array
    {
        return $this->deliveryServiceRestrictPackageRepository->all($query->isActive);
    }
}
