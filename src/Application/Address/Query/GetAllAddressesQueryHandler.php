<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;

readonly class GetAllAddressesQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(GetAllAddressesQuery $query): array
    {
        return $this->regionRepository->all();
    }
}
