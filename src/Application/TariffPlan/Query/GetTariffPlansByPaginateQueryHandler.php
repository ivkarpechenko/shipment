<?php

namespace App\Application\TariffPlan\Query;

use App\Application\QueryHandler;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class GetTariffPlansByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function __invoke(GetTariffPlansByPaginateQuery $query): array
    {
        return $this->repository->paginate($query->getPage(), $query->getOffset());
    }
}
