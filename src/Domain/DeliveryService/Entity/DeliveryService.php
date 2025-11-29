<?php

namespace App\Domain\DeliveryService\Entity;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'delivery_service')]
#[ORM\Entity(repositoryClass: DeliveryServiceRepositoryInterface::class)]
#[ORM\Index(columns: ['name'], name: 'idx_delivery_service_name', options: ['where' => '(is_active = true)'])]
#[ORM\Index(columns: ['created_at'], name: 'idx_delivery_service_created_at', options: ['where' => '(is_active = true)'])]
class DeliveryService
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: DeliveryMethod::class, mappedBy: 'deliveryServices', cascade: ['persist', 'remove'])]
    private Collection $deliveryMethods;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->createdAt = new \DateTime('now');

        $this->deliveryMethods = new ArrayCollection();
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

    public function getDeliveryMethods(): Collection
    {
        return $this->deliveryMethods;
    }

    public function addDeliveryMethod(DeliveryMethod $deliveryMethod): void
    {
        if (!$this->deliveryMethods->contains($deliveryMethod)) {
            $this->deliveryMethods->add($deliveryMethod);
            $deliveryMethod->addDeliveryService($this);

            $this->updatedAt = new \DateTime('now');
        }
    }

    public function removeDeliveryMethod(DeliveryMethod $deliveryMethod): void
    {
        if ($this->deliveryMethods->contains($deliveryMethod)) {
            $deliveryMethod->removeDeliveryService($this);
            $this->deliveryMethods->removeElement($deliveryMethod);

            $this->updatedAt = new \DateTime('now');
        }
    }
}
