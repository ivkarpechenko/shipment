<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Address\Exception\AddressDeactivatedException;
use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class CheckAddressInRestrictedAreaService
{
    public function __construct(
        public DeliveryServiceRestrictAreaRepositoryInterface $deliveryServiceRestrictAreaRepository,
        public DeliveryServiceRepositoryInterface $deliveryServiceRepository,
        public AddressRepositoryInterface $addressRepository
    ) {
    }

    public function check(Uuid $deliveryServiceId, Uuid $addressId): bool
    {
        $deliveryService = $this->deliveryServiceRepository->ofId($deliveryServiceId);
        if (is_null($deliveryService)) {
            $deliveryService = $this->deliveryServiceRepository->ofIdDeactivated($deliveryServiceId);
            if (!is_null($deliveryService)) {
                throw new DeliveryServiceDeactivatedException(sprintf('Delivery service with code %s deactivated', $deliveryService->getCode()));
            }

            throw new DeliveryServiceNotFoundException(sprintf('DeliveryService with ID %s not found', $deliveryServiceId->toRfc4122()));
        }

        $address = $this->addressRepository->ofId($addressId);
        if (is_null($address)) {
            $address = $this->addressRepository->ofAddressDeactivated($address);
            if (!is_null($address)) {
                throw new AddressDeactivatedException(sprintf('Address %s deactivated', $address->getAddress()));
            }

            throw new AddressNotFoundException(sprintf('Address with ID %s not found', $addressId->toRfc4122()));
        }

        if (is_null($address->getPoint())) {
            return false;
        }

        $restrictedAreas = $this
            ->deliveryServiceRestrictAreaRepository
            ->ofDeliveryServiceIdAndPoint($deliveryService->getId(), $address->getPoint());

        return !empty($restrictedAreas);
    }
}
