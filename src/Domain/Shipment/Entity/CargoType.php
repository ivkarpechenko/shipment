<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Entity;

use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'cargo_type')]
#[ORM\Entity(repositoryClass: CargoTypeRepositoryInterface::class)]
#[ORM\Index(columns: ['name'], name: 'idx_cargo_type_name', options: ['where' => '(is_active = true)'])]
#[ORM\Index(columns: ['created_at'], name: 'idx_cargo_type_created_at', options: ['where' => '(is_active = true)'])]
class CargoType
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isActive;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->isActive = true;
        $this->createdAt = new \DateTime('now');
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

    public function change(string $name, bool $isActive): void
    {
        $this->name = $name;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
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
}
