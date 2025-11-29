<?php

namespace App\Domain\TariffPlan\Entity;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'tariff_plan')]
#[ORM\UniqueConstraint(
    name: 'tariff_plan_unique_code',
    columns: ['delivery_service_id', 'delivery_method_id', 'code']
)]
#[ORM\Entity(repositoryClass: TariffPlanRepositoryInterface::class)]
#[ORM\Index(columns: ['name'], name: 'idx_tariff_plan_name', options: ['where' => '(is_active = true)'])]
#[ORM\Index(columns: ['created_at'], name: 'idx_tariff_plan_created_at', options: ['where' => '(is_active = true)'])]
class TariffPlan
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: DeliveryService::class)]
    #[ORM\JoinColumn(nullable: false)]
    private DeliveryService $deliveryService;

    #[ORM\ManyToOne(targetEntity: DeliveryMethod::class)]
    #[ORM\JoinColumn(nullable: false)]
    private DeliveryMethod $deliveryMethod;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(DeliveryService $deliveryService, DeliveryMethod $deliveryMethod, string $code, string $name)
    {
        $this->deliveryService = $deliveryService;
        $this->deliveryMethod = $deliveryMethod;
        $this->code = $code;
        $this->name = $name;
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

    public function getDeliveryMethod(): DeliveryMethod
    {
        return $this->deliveryMethod;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }
}
