<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Currency\Entity\Currency;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

readonly class CdekShipmentDto
{
    public function __construct(
        public Uuid $id,
        public Address $from,
        public Address $to,
        public Contact $sender,
        public Contact $recipient,
        public TariffPlan $tariffPlan,
        public Currency $currency,
        public Collection $packages,
        public \DateTime $psd,
        public \DateTime $psdStartTime,
        public \DateTime $psdEndTime,
        public \DateTime $createdAt,
        public ?\DateTime $updatedAt,
        public ?PickupPoint $pickupPoint,
    ) {
    }

    public static function fromShipmentAndTariffPlan(Shipment $shipment, TariffPlan $tariffPlan): CdekShipmentDto
    {
        $packages = new ArrayCollection();
        foreach ($shipment->getPackages() as $package) {
            $packages->add(CdekPackageDto::fromPackage($package));
        }

        return new self(
            $shipment->getId(),
            $shipment->getFrom(),
            $shipment->getTo(),
            $shipment->getSender(),
            $shipment->getRecipient(),
            $tariffPlan,
            $shipment->getCurrency(),
            $packages,
            $shipment->getPsd(),
            $shipment->getPsdStartTime(),
            $shipment->getPsdEndTime(),
            $shipment->getCreatedAt(),
            $shipment->getUpdatedAt(),
            $shipment->getPickupPoint(),
        );
    }
}
