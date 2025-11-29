<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;

readonly class FindCalculateByIdQueryHandler implements QueryHandler
{
    public function __construct(public CalculateRepositoryInterface $calculateRepository)
    {
    }

    public function __invoke(FindCalculateByIdQuery $query): ?Calculate
    {
        return $this->calculateRepository->ofIdNotExpired($query->getCalculateId());
    }
}
