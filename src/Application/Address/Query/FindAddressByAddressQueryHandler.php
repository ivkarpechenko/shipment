<?php

namespace App\Application\Address\Query;

use App\Application\Address\Query\External\FindExternalAddressQuery;
use App\Application\QueryBus;
use App\Application\QueryHandler;
use App\Domain\Address\Entity\Address;
use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressByAddressQueryHandler implements QueryHandler
{
    public function __construct(
        public AddressRepositoryInterface $addressRepository,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(FindAddressByAddressQuery $query): ?Address
    {
        $address = $this->addressRepository->ofAddress($query->getAddress());

        if (is_null($address)) {
            $dto = $this->queryBus->handle(new FindExternalAddressQuery($query->getAddress()));
            if (is_null($dto)) {
                throw new AddressNotFoundException(sprintf(
                    'The external source did not find the transmitted address %s',
                    $query->getAddress()
                ));
            }
            $address = $this->addressRepository->ofAddress($dto->address);
        }

        return $address;
    }
}
