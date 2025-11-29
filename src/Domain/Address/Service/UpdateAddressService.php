<?php

namespace App\Domain\Address\Service;

use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateAddressService
{
    public function __construct(public AddressRepositoryInterface $repository)
    {
    }

    public function update(Uuid $addressId, ?bool $isActive): void
    {
        $address = $this->repository->ofId($addressId);

        if (is_null($address)) {
            $address = $this->repository->ofIdDeactivated($addressId);

            if (is_null($address)) {
                throw new AddressNotFoundException(sprintf('Address with ID: %s not found', $addressId->toRfc4122()));
            }
        }

        if (!is_null($isActive) && !$address->equalsIsActive($isActive)) {
            $address->changeIsActive($isActive);
        }

        $this->repository->update($address);
    }
}
