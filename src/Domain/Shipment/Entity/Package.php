<?php

namespace App\Domain\Shipment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table('package')]
class Package
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Shipment::class, inversedBy: 'packages')]
    private ?Shipment $shipment;

    #[ORM\OneToMany(mappedBy: 'package', targetEntity: PackageProduct::class, cascade: ['persist', 'remove'])]
    private Collection $products;

    /**
     * Total cost of products inside the package
     */
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: false)]
    private string $price;

    /**
     * Total width (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $width;

    /**
     * Total height (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $height;

    /**
     * Total length (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $length;

    /**
     * Total weight (in grams)
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $weight;

    public function __construct(string $price, int $width, int $height, int $length, int $weight)
    {
        $this->price = $price;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->weight = $weight;

        $this->products = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getVolume(): int
    {
        return $this->length * $this->width * $this->height;
    }

    public function setShipment(?Shipment $shipment): void
    {
        $this->shipment = $shipment;
    }

    public function addProduct(PackageProduct $packageProduct): void
    {
        if (!$this->products->contains($packageProduct)) {
            $this->products[] = $packageProduct;
            $packageProduct->setPackage($this);
        }
    }

    public function removeProduct(PackageProduct $packageProduct): void
    {
        if ($this->products->contains($packageProduct)) {
            $this->products->removeElement($packageProduct);
            $packageProduct->setPackage(null);
        }
    }
}
