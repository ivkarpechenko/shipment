<?php

namespace App\Domain\Address\Service;

use App\Domain\Address\Entity\Address;
use App\Domain\Address\Exception\AddressAlreadyCreatedException;
use App\Domain\Address\Exception\AddressDeactivatedException;
use App\Domain\Address\Exception\AddressDeletedException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\Service\Dto\AddressDto;
use App\Domain\Address\ValueObject\Point;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;

readonly class CreateAddressService
{
    public function __construct(
        public AddressRepositoryInterface $repositoryAddress,
        public CityRepositoryInterface $cityRepository
    ) {
    }

    public function create(AddressDto $dto): void
    {
        $city = $this->cityRepository->ofTypeAndName($dto->cityType, $dto->city);
        if (is_null($city)) {
            throw new CityNotFoundException(sprintf('City with type %s and name %s not found', $dto->cityType, $dto->city));
        }

        $address = $this->repositoryAddress->ofAddress($dto->address);
        if (!is_null($address)) {
            throw new AddressAlreadyCreatedException(sprintf('Address %s already created', $dto->address));
        }

        $address = $this->repositoryAddress->ofAddressDeactivated($dto->address);
        if (!is_null($address)) {
            throw new AddressDeactivatedException(sprintf('Address %s deactivated', $dto->address));
        }

        $address = $this->repositoryAddress->ofAddressDeleted($dto->address);
        if (!is_null($address)) {
            throw new AddressDeletedException('Address deleted');
        }

        $point = null;
        if (!is_null($dto->latitude) && !is_null($dto->longitude)) {
            $point = new Point($dto->latitude, $dto->longitude);
        }

        $address = new Address(
            $city,
            $dto->address,
            $dto->house,
            $point,
            $dto->postalCode,
            $dto->street,
            $dto->flat,
            $dto->entrance,
            $dto->floor,
            $dto->settlement,
            $dto->inputData
        );

        $this->repositoryAddress->create($address);
    }
}
