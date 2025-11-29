<?php

namespace App\Domain\Address\Service;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class DeleteAddressService
{
    public function __construct(public AddressRepositoryInterface $repository)
    {
    }

    public function delete(Uuid $addressId): void
    {
        $address = $this->repository->ofId($addressId);

        if (is_null($address)) {
            return;
        }

        $address->deleted();

        $this->repository->delete($address);
    }
}
