<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;

readonly class FindDeliveryMethodByCodeQueryHandler implements QueryHandler
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository
    ) {
    }

    public function __invoke(FindDeliveryMethodByCodeQuery $query): ?DeliveryMethod
    {
        return $this->deliveryMethodRepository->ofCode($query->code);
    }
}
