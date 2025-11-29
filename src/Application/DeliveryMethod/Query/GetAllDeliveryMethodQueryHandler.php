<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;

readonly class GetAllDeliveryMethodQueryHandler implements QueryHandler
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository,
    ) {
    }

    public function __invoke(GetAllDeliveryMethodQuery $query): array
    {
        return $this->deliveryMethodRepository->all($query->isActive);
    }
}
