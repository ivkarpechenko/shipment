<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class FindTariffPlanByCodeQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(FindTariffPlanByCodeQuery $query): ?TariffPlan
    {
        return $this->repository->ofCode($query->deliveryServiceCode, $query->deliveryMethodCode, $query->code);
    }
}
