<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class FindTariffPlanByIdQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(FindTariffPlanByIdQuery $query): ?TariffPlan
    {
        return $this->repository->ofId($query->getTariffPlanId());
    }
}
