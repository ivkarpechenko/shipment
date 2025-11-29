<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;

readonly class FindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryHandler implements QueryHandler
{
    public function __construct(
        public DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepository
    ) {
    }

    public function __invoke(FindDeliveryServiceRestrictPackageByDeliveryServiceIdQuery $query): ?DeliveryServiceRestrictPackage
    {
        return $this->deliveryServiceRestrictPackageRepository->ofDeliveryServiceId($query->deliveryServiceId);
    }
}
