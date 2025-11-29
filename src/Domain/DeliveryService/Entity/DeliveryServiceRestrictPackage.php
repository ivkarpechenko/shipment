<?php

declare(strict_types=1);

namespace App\Domain\DeliveryService\Entity;

use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'delivery_service_restrict_package')]
#[ORM\Entity(repositoryClass: DeliveryServiceRestrictPackageRepositoryInterface::class)]
#[ORM\UniqueConstraint(name: 'uidx_delivery_service_restrict_package_delivery_service_id', columns: ['delivery_service_id'])]
class DeliveryServiceRestrictPackage
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: DeliveryService::class)]
    #[ORM\JoinColumn(nullable: false)]
    private DeliveryService $deliveryService;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxWeight;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxWidth;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxHeight;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $maxLength;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isActive;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt;

    public function __construct(DeliveryService $deliveryService, int $maxWeight, int $maxWidth, int $maxHeight, int $maxLength)
    {
        $this->deliveryService = $deliveryService;
        $this->maxWeight = $maxWeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->maxLength = $maxLength;
        $this->isActive = true;
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDeliveryService(): DeliveryService
    {
        return $this->deliveryService;
    }

    public function getMaxWeight(): int
    {
        return $this->maxWeight;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTime();
    }

    public function change(int $maxWeight, int $maxWidth, int $maxHeight, int $maxLength, bool $isActive): void
    {
        $this->maxWeight = $maxWeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->maxLength = $maxLength;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }
}
