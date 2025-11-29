<?php

namespace App\Domain\Shipment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table('product')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'products')]
    private ?Store $store;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: PackageProduct::class)]
    private Collection $packages;

    /**
     * Code
     */
    #[ORM\Column(type: 'string', nullable: false)]
    private string $code;

    /**
     * Description
     */
    #[ORM\Column(type: 'text', nullable: false)]
    private string $description;

    /**
     * Unit cost
     */
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: false)]
    private string $price;

    /**
     * Product weight (in grams)
     */
    #[ORM\Column(type: 'float', nullable: false, options: ['default' => 0])]
    private float $weight;

    /**
     * Product width (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $width;

    /**
     * Product height (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $height;

    /**
     * Product length (in millimeters)
     */
    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $length;

    /**
     * Quantity of products
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $quantity;

    /**
     * Is the product fragile?
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isFragile;

    /**
     * Is this flammable product?
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isFlammable;

    /**
     * Is this can rotate product?
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isCanRotate;

    /**
     * Delivery period
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $deliveryPeriod = 0;

    public function __construct(
        string $code,
        string $description,
        string $price,
        float $weight,
        int $width,
        int $height,
        int $length,
        int $quantity,
        bool $isFragile = false,
        bool $isFlammable = false,
        bool $isCanRotate = false,
        int $deliveryPeriod = 0
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->price = $price;
        $this->weight = $weight;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->quantity = $quantity;
        $this->isFragile = $isFragile;
        $this->isFlammable = $isFlammable;
        $this->isCanRotate = $isCanRotate;
        $this->deliveryPeriod = $deliveryPeriod;

        $this->packages = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPackages(): Collection
    {
        return $this->packages;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    // in kilograms
    public function getWeight(): float
    {
        return $this->weight;
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

    public function getVolume(): int
    {
        return $this->length * $this->width * $this->height;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDeliveryPeriod(): int
    {
        return $this->deliveryPeriod;
    }

    public function isFragile(): bool
    {
        return $this->isFragile;
    }

    public function isFlammable(): bool
    {
        return $this->isFlammable;
    }

    public function isCanRotate(): bool
    {
        return $this->isCanRotate;
    }

    public function getDimensions(): array
    {
        return [$this->width, $this->length, $this->height];
    }

    public function sortDimensions(): array
    {
        $dimensions = $this->getDimensions();

        sort($dimensions);

        return $dimensions;
    }

    public function setStore(?Store $store): void
    {
        $this->store = $store;
    }

    public function addPackage(PackageProduct $packageProduct): void
    {
        if (!$this->packages->contains($packageProduct)) {
            $this->packages[] = $packageProduct;
            $packageProduct->setProduct($this);
        }
    }

    public function removePackage(PackageProduct $packageProduct): void
    {
        if ($this->packages->contains($packageProduct)) {
            $this->packages->removeElement($packageProduct);
            $packageProduct->setProduct(null);
        }
    }

    public function changeWidth(int $width): void
    {
        $this->width = $width;
    }

    public function changeHeight(int $height): void
    {
        $this->height = $height;
    }

    public function changeLength(int $length): void
    {
        $this->length = $length;
    }
}
