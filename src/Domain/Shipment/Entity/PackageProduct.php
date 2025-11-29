<?php

namespace App\Domain\Shipment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table('package_product')]
class PackageProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Package::class, cascade: ['persist', 'remove'], inversedBy: 'packages')]
    private ?Package $package;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist', 'remove'], inversedBy: 'products')]
    private ?Product $product;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setPackage(?Package $package): void
    {
        $this->package = $package;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function incrementQuantity(): void
    {
        ++$this->quantity;
    }
}
