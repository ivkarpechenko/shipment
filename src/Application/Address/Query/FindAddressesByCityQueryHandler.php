<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressesByCityQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAddressesByCityQuery $query): array
    {
        return $this->regionRepository->ofCity($query->getCity());
    }
}
