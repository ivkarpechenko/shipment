<?php

namespace App\Domain\Shipment\Entity;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Currency\Entity\Currency;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'shipment')]
#[ORM\Entity(repositoryClass: ShipmentRepositoryInterface::class)]
#[ORM\Index(columns: ['created_at'], name: 'idx_shipment_created_at')]
class Shipment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: '`from`', referencedColumnName: 'id')]
    private Address $from;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: '`to`', referencedColumnName: 'id')]
    private Address $to;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'sender', referencedColumnName: 'id')]
    private Contact $sender;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'recipient', referencedColumnName: 'id')]
    private Contact $recipient;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(name: 'currency', referencedColumnName: 'id')]
    private Currency $currency;

    #[ORM\ManyToOne(targetEntity: PickupPoint::class)]
    #[ORM\JoinColumn(name: '`pickup_point`', referencedColumnName: 'id', nullable: true)]
    private ?PickupPoint $pickupPoint;

    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: Package::class, cascade: ['persist', 'remove'])]
    private Collection $packages;

    #[ORM\Column(type: 'date', nullable: false)]
    private \DateTime $psd;

    #[ORM\Column(type: 'time', nullable: false)]
    private \DateTime $psdStartTime;

    #[ORM\Column(type: 'time', nullable: false)]
    private \DateTime $psdEndTime;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(
        Address $from,
        Address $to,
        Contact $sender,
        Contact $recipient,
        Currency $currency,
        \DateTime $psd,
        \DateTime $psdStartTime,
        \DateTime $psdEndTime
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->currency = $currency;
        $this->packages = new ArrayCollection();
        $this->psd = $psd;
        $this->psdStartTime = $psdStartTime;
        $this->psdEndTime = $psdEndTime;
        $this->createdAt = new \DateTime('now');
        $this->pickupPoint = null;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFrom(): Address
    {
        return $this->from;
    }

    public function getTo(): Address
    {
        return $this->to;
    }

    public function getSender(): Contact
    {
        return $this->sender;
    }

    public function getRecipient(): Contact
    {
        return $this->recipient;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getPackages(): Collection
    {
        return $this->packages;
    }

    public function getPsd(): ?\DateTime
    {
        return $this->psd;
    }

    public function getPsdStartTime(): \DateTime
    {
        return $this->psdStartTime;
    }

    public function getPsdEndTime(): \DateTime
    {
        return $this->psdEndTime;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function addPackage(Package $package): void
    {
        if (!$this->packages->contains($package)) {
            $package->setShipment($this);
            $this->packages[] = $package;

            $this->updatedAt = new \DateTime('now');
        }
    }

    public function removePackage(Package $package): void
    {
        if ($this->packages->contains($package)) {
            $this->packages->removeElement($package);
            // set the owning side to null (unless already changed)
            if ($package->getShipment() === $this) {
                $package->setShipment(null);
            }

            $this->updatedAt = new \DateTime('now');
        }
    }

    public function changeFrom(Address $from): void
    {
        $this->from = $from;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeTo(Address $to): void
    {
        $this->to = $to;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeSender(Contact $sender): void
    {
        $this->sender = $sender;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeRecipient(Contact $recipient): void
    {
        $this->recipient = $recipient;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeCurrency(Currency $currency): void
    {
        $this->currency = $currency;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePsd(\DateTime $psd): void
    {
        $this->psd = $psd;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePsdStartTime(\DateTime $psdStartTime): void
    {
        $this->psdStartTime = $psdStartTime;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePsdEndTime(\DateTime $psdEndTime): void
    {
        $this->psdEndTime = $psdEndTime;
        $this->updatedAt = new \DateTime('now');
    }

    public function equalsPsd(\DateTime $psd): bool
    {
        return $this->psd === $psd;
    }

    public function equalsPsdStartTime(\DateTime $psdStartTime): bool
    {
        return $this->psdStartTime === $psdStartTime;
    }

    public function equalsPsdEndTime(\DateTime $psdEndTime): bool
    {
        return $this->psdEndTime === $psdEndTime;
    }

    public function changePickupPoint(?PickupPoint $pickupPoint): void
    {
        $this->pickupPoint = $pickupPoint;
        $this->updatedAt = new \DateTime('now');
    }

    public function getPickupPoint(): ?PickupPoint
    {
        return $this->pickupPoint;
    }
}
