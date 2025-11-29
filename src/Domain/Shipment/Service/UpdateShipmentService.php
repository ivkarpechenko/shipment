<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Address\Exception\AddressDeactivatedException;
use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateShipmentService
{
    public function __construct(
        public AddressRepositoryInterface $addressRepository,
        public ContactRepositoryInterface $contactRepository,
        public CurrencyRepositoryInterface $currencyRepository,
        public ShipmentRepositoryInterface $shipmentRepository,
        public PickupPointRepositoryInterface $pickupPointRepository
    ) {
    }

    public function update(
        Uuid $shipmentId,
        ?string $from = null,
        ?string $to = null,
        ?Uuid $senderId = null,
        ?Uuid $recipientId = null,
        ?string $currencyCode = null,
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
        ?Uuid $pickupPointId = null,
    ): void {
        $shipment = $this->shipmentRepository->ofId($shipmentId);
        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf('Shipment with ID %s was not found', $shipmentId->toRfc4122()));
        }

        if (!is_null($from)) {
            $fromAddress = $this->addressRepository->ofAddress($from);
            if (is_null($fromAddress)) {
                $fromAddress = $this->addressRepository->ofAddressDeactivated($from);
                if (!is_null($fromAddress)) {
                    throw new AddressDeactivatedException(sprintf('Address %s deactivated', $from));
                }

                throw new AddressNotFoundException(sprintf('Address %s was not found', $from));
            }

            $shipment->changeFrom($fromAddress);
        }

        if (!is_null($to)) {
            $toAddress = $this->addressRepository->ofAddress($to);
            if (is_null($toAddress)) {
                $toAddress = $this->addressRepository->ofAddressDeactivated($to);
                if (!is_null($toAddress)) {
                    throw new AddressDeactivatedException(sprintf('Address %s deactivated', $to));
                }

                throw new AddressNotFoundException(sprintf('Address %s was not found', $to));
            }

            $shipment->changeTo($toAddress);
        }

        if (!is_null($senderId)) {
            $sender = $this->contactRepository->ofId($senderId);
            if (is_null($sender)) {
                throw new ContactNotFoundException(sprintf('Contact with ID %s was not found', $senderId->toRfc4122()));
            }

            $shipment->changeSender($sender);
        }

        if (!is_null($recipientId)) {
            $recipient = $this->contactRepository->ofId($recipientId);
            if (is_null($recipient)) {
                throw new ContactNotFoundException(sprintf('Contact with ID %s was not found', $recipientId->toRfc4122()));
            }

            $shipment->changeRecipient($recipient);
        }

        if (!is_null($currencyCode)) {
            $currency = $this->currencyRepository->ofCode($currencyCode);
            if (is_null($currency)) {
                $currency = $this->currencyRepository->ofCodeDeactivated($currencyCode);
                if (!is_null($currency)) {
                    throw new CurrencyDeactivatedException(sprintf('Currency with code %s deactivated', $currencyCode));
                }

                throw new CurrencyNotFoundException(sprintf('Currency with code %s was not found', $currencyCode));
            }

            $shipment->changeCurrency($currency);
        }

        if (!is_null($psd)) {
            if (!$shipment->equalsPsd($psd)) {
                $shipment->changePsd($psd);
            }
        }

        if (!is_null($psdStartTime)) {
            if (!$shipment->equalsPsdStartTime($psdStartTime)) {
                $shipment->changePsdStartTime($psdStartTime);
            }
        }

        if (!is_null($psdEndTime)) {
            if (!$shipment->equalsPsdEndTime($psdEndTime)) {
                $shipment->changePsdEndTime($psdEndTime);
            }
        }

        if (!is_null($pickupPointId)) {
            $pickupPoint = $this->pickupPointRepository->ofId($pickupPointId);
            $shipment->changePickupPoint($pickupPoint);
        }

        if (!empty($packages)) {
            /*
             * TODO delete if you want to keep the old packages.
             * Remove current packages
             */
            foreach ($shipment->getPackages() as $package) {
                $shipment->removePackage($package);
            }

            // Add new packages
            foreach ($packages as $package) {
                $shipment->addPackage($package);
            }
        }

        $this->shipmentRepository->update($shipment);
    }
}
