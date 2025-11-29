<?php

namespace App\Application\Address\Query\External;

use App\Application\QueryHandler;
use App\Domain\Address\Service\Dto\AddressDto;

class FindExternalAddressQueryHandler implements QueryHandler
{
    public function __construct(public FindExternalAddressInterface $findExternalAddress)
    {
    }

    public function __invoke(FindExternalAddressQuery $query): ?AddressDto
    {
        return $this->findExternalAddress->find($query->getAddress());
    }
}
