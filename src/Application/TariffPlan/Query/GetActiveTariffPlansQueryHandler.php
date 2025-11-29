<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class GetActiveTariffPlansQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(GetActiveTariffPlansQuery $query): array
    {
        return $this->repository->active();
    }
}
