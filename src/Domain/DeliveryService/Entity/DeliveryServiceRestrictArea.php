<?php

namespace App\Domain\DeliveryService\Entity;

use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'delivery_service_restrict_area')]
#[ORM\Entity(repositoryClass: DeliveryServiceRestrictAreaRepositoryInterface::class)]
#[ORM\Index(columns: ['created_at'], name: 'idx_delivery_service_restrict_area_created_at', options: ['where' => '(is_active = true)'])]
class DeliveryServiceRestrictArea
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: DeliveryService::class)]
    #[ORM\JoinColumn(name: 'delivery_service', referencedColumnName: 'id')]
    private DeliveryService $deliveryService;

    #[ORM\Column(type: 'string', nullable: true)]
    private string $name;

    #[ORM\Column(type: 'geometry', nullable: false)]
    private Polygon $polygon;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(DeliveryService $deliveryService, string $name, Polygon $polygon)
    {
        $this->deliveryService = $deliveryService;
        $this->name = $name;
        $this->polygon = $polygon;

        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDeliveryService(): DeliveryService
    {
        return $this->deliveryService;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPolygon(): Polygon
    {
        return $this->polygon;
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

    public function isEqualPolygon(Polygon $polygon): bool
    {
        return $this->polygon->isEqual($polygon);
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePolygon(Polygon $polygon): void
    {
        $this->polygon = $polygon;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }
}
