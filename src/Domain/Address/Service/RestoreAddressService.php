<?php

namespace App\Domain\Address\Service;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class RestoreAddressService
{
    public function __construct(public AddressRepositoryInterface $repository)
    {
    }

    public function restore(Uuid $addressId): void
    {
        $address = $this->repository->ofIdDeleted($addressId);

        if (is_null($address)) {
            return;
        }

        $address->restored();

        $this->repository->restore($address);
    }
}
