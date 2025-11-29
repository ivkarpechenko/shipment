<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class GetAllTariffPlansQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(GetAllTariffPlansQuery $query): array
    {
        return $this->repository->all();
    }
}
