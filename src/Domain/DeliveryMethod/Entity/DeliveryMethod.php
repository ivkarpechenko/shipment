<?php

declare(strict_types=1);

namespace App\Domain\DeliveryMethod\Entity;

use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'delivery_method')]
#[ORM\Entity(repositoryClass: DeliveryMethodRepositoryInterface::class)]
#[ORM\Index(columns: ['name'], name: 'idx_delivery_method_name', options: ['where' => '(is_active = true)'])]
#[ORM\Index(columns: ['created_at'], name: 'idx_delivery_method_created_at', options: ['where' => '(is_active = true)'])]
class DeliveryMethod
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isActive;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: DeliveryService::class, inversedBy: 'deliveryMethods', cascade: ['persist', 'remove'])]
    private Collection $deliveryServices;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->isActive = true;
        $this->createdAt = new \DateTime('now');

        $this->deliveryServices = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime('now');
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTime('now');
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

    public function getDeliveryServices(): Collection
    {
        return $this->deliveryServices;
    }

    public function addDeliveryService(DeliveryService $deliveryService): void
    {
        if (!$this->deliveryServices->contains($deliveryService)) {
            $this->deliveryServices->add($deliveryService);
            $deliveryService->addDeliveryMethod($this);

            $this->updatedAt = new \DateTime('now');
        }
    }

    public function removeDeliveryService(DeliveryService $deliveryService): void
    {
        if ($this->deliveryServices->contains($deliveryService)) {
            $deliveryService->removeDeliveryMethod($this);
            $this->deliveryServices->removeElement($deliveryService);

            $this->updatedAt = new \DateTime('now');
        }
    }
}
