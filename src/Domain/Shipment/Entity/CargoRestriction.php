<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Entity;

use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'cargo_restriction')]
#[ORM\Entity(repositoryClass: CargoRestrictionRepositoryInterface::class)]
class CargoRestriction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: CargoType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private CargoType $cargoType;

    #[ORM\ManyToOne(targetEntity: Shipment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Shipment $shipment;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxWidth;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxHeight;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxLength;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxWeight;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxVolume;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxSumDimensions;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(
        CargoType $cargoType,
        Shipment $shipment,
        int $maxWidth,
        int $maxHeight,
        int $maxLength,
        int $maxWeight,
        int $maxVolume,
        int $maxSumDimensions
    ) {
        $this->cargoType = $cargoType;
        $this->shipment = $shipment;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->maxLength = $maxLength;
        $this->maxWeight = $maxWeight;
        $this->maxVolume = $maxVolume;
        $this->maxSumDimensions = $maxSumDimensions;
        $this->createdAt = new \DateTime();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCargoType(): CargoType
    {
        return $this->cargoType;
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function getMaxWeight(): int
    {
        return $this->maxWeight;
    }

    public function getMaxVolume(): int
    {
        return $this->maxVolume;
    }

    public function getMaxSumDimensions(): int
    {
        return $this->maxSumDimensions;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
