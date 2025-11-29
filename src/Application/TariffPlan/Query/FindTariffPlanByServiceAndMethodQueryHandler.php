<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class FindTariffPlanByServiceAndMethodQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(FindTariffPlanByServiceAndMethodQuery $query): array
    {
        return $this->repository->ofServiceAndMethod($query->deliveryServiceCode, $query->deliveryMethodCode, true);
    }
}
