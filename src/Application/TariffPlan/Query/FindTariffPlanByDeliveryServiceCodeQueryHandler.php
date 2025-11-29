<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class FindTariffPlanByDeliveryServiceCodeQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(FindTariffPlanByDeliveryServiceCodeQuery $query): array
    {
        return $this->repository->ofDeliveryServiceCode($query->getDeliveryServiceCode());
    }
}
