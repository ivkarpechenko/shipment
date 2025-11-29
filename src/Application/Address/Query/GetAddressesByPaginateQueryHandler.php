<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;

readonly class GetAddressesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetAddressesByPaginateQuery $query): array
    {
        return $this->regionRepository->paginate($query->getPage(), $query->getOffset());
    }
}
