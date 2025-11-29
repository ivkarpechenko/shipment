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
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Exception\InvalidPsdException;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateShipmentService
{
    public function __construct(
        public AddressRepositoryInterface $addressRepository,
        public ContactRepositoryInterface $contactRepository,
        public CurrencyRepositoryInterface $currencyRepository,
        public ShipmentRepositoryInterface $shipmentRepository,
        public CheckAddressInRestrictedAreaService $addressInRestrictedAreaService
    ) {
    }

    public function create(
        string $from,
        string $to,
        Uuid $senderId,
        Uuid $recipientId,
        string $currencyCode,
        array $packages,
        \DateTime $psd,
        \DateTime $psdStartTime,
        \DateTime $psdEndTime
    ): Uuid {
        $fromAddress = $this->addressRepository->ofAddress($from);
        if (is_null($fromAddress)) {
            $fromAddress = $this->addressRepository->ofAddressDeactivated($from);
            if (!is_null($fromAddress)) {
                throw new AddressDeactivatedException(sprintf('Address %s deactivated', $from));
            }

            throw new AddressNotFoundException(sprintf(
                'Shipment from address %s invalid',
                $from
            ));
        }

        $toAddress = $this->addressRepository->ofAddress($to);
        if (is_null($toAddress)) {
            $toAddress = $this->addressRepository->ofAddressDeactivated($to);
            if (!is_null($toAddress)) {
                throw new AddressDeactivatedException(sprintf('Address %s deactivated', $to));
            }

            throw new AddressNotFoundException(sprintf(
                'Shipment to address %s invalid',
                $to
            ));
        }

        $sender = $this->contactRepository->ofId($senderId);
        if (is_null($sender)) {
            throw new ContactNotFoundException(sprintf(
                'Shipment sender contact with ID %s was not found',
                $senderId->toRfc4122()
            ));
        }

        $recipient = $this->contactRepository->ofId($recipientId);
        if (is_null($recipient)) {
            throw new ContactNotFoundException(sprintf(
                'Shipment recipient contact with ID %s was not found',
                $recipientId->toRfc4122()
            ));
        }

        $currency = $this->currencyRepository->ofCode($currencyCode);
        if (is_null($currency)) {
            $currency = $this->currencyRepository->ofCodeDeactivated($currencyCode);
            if (!is_null($currency)) {
                throw new CurrencyDeactivatedException(sprintf('Currency with code %s deactivated', $currencyCode));
            }

            throw new CurrencyNotFoundException(sprintf('Currency with code %s was not found', $currencyCode));
        }

        // Check psd field the past date
        if ($psd->format('Y-m-d') < (new \DateTime('now'))->format('Y-m-d')) {
            throw new InvalidPsdException('The psd field can contain the past date');
        }

        $shipment = new Shipment(
            $fromAddress,
            $toAddress,
            $sender,
            $recipient,
            $currency,
            $psd,
            $psdStartTime,
            $psdEndTime
        );

        foreach ($packages as $package) {
            $shipment->addPackage($package);
        }

        return $this->shipmentRepository->create($shipment);
    }
}
