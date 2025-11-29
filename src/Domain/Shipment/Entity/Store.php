<?php

namespace App\Domain\Shipment\Entity;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table('store')]
class Store
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact', referencedColumnName: 'id')]
    private Contact $contact;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address', referencedColumnName: 'id')]
    private Address $address;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Product::class, cascade: ['persist', 'remove'])]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: StoreSchedule::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $schedules;

    /**
     * @deprecated
     * Store external identifier (lennuf)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $externalId;

    /**
     * Max. weight of products (in grams)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxWeight;

    /**
     * Max. volume of products (in liters)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxVolume;

    /**
     * Max. length of one of the sides of the product (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxLength;

    /**
     * Is it possible to pick up from the store?
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isPickup = false;

    /**
     * @deprecated
     * Scheduled delivery date
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $psd;

    /**
     * @deprecated
     * The beginning of the working day of the store
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $psdStartTime;

    /**
     * @deprecated
     * The end of the working day of the store
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $psdEndTime;

    public function __construct(
        Contact $contact,
        Address $address,
        int $externalId,
        int $maxWeight,
        int $maxVolume,
        int $maxLength,
        bool $isPickup = false,
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null
    ) {
        $this->contact = $contact;
        $this->address = $address;
        $this->externalId = $externalId;
        $this->maxWeight = $maxWeight;
        $this->maxVolume = $maxVolume;
        $this->maxLength = $maxLength;
        $this->isPickup = $isPickup;
        $this->psd = $psd;
        $this->psdStartTime = $psdStartTime;
        $this->psdEndTime = $psdEndTime;

        $this->products = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function getMaxWeight(): int
    {
        return $this->maxWeight;
    }

    public function getMaxVolume(): int
    {
        return $this->maxVolume;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function isPickup(): bool
    {
        return $this->isPickup;
    }

    public function getPsd(): ?\DateTime
    {
        return $this->psd;
    }

    public function getPsdStartTime(): ?\DateTime
    {
        return $this->psdStartTime;
    }

    public function getPsdEndTime(): ?\DateTime
    {
        return $this->psdEndTime;
    }

    public function addProduct(Product $product): void
    {
        if (!$this->products->contains($product)) {
            $product->setStore($this);
            $this->products[] = $product;
        }
    }

    public function removeProduct(Product $product): void
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getStore() === $this) {
                $product->setStore(null);
            }
        }
    }

    public function addSchedule(StoreSchedule $schedule): void
    {
        if (!$this->schedules->contains($schedule)) {
            $schedule->setStore($this);
            $this->schedules[] = $schedule;
        }
    }

    public function removeSchedule(StoreSchedule $schedule): void
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getStore() === $this) {
                $schedule->setStore(null);
            }
        }
    }
}
