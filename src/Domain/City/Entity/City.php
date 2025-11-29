<?php

namespace App\Domain\City\Entity;

use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Region\Entity\Region;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'city')]
#[ORM\UniqueConstraint(
    name: 'city_unique_region_id_type_name',
    columns: ['region_id', 'type', 'name']
)]
#[ORM\Entity(repositoryClass: CityRepositoryInterface::class)]
#[ORM\Index(columns: ['created_at'], name: 'idx_city_created_at', options: ['where' => '(deleted_at IS NULL)'])]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Region $region;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $type;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $deletedAt = null;

    public function __construct(Region $region, string $type, string $name)
    {
        $this->region = $region;
        $this->type = $type;
        $this->name = $name;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function changeType(string $type): void
    {
        $this->type = $type;
        $this->updatedAt = new DateTime('now');
    }

    public function changeRegion(Region $region): void
    {
        $this->region = $region;
        $this->updatedAt = new DateTime('now');
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new DateTime('now');
    }

    public function deleted(): void
    {
        $this->deletedAt = new DateTime('now');
    }

    public function restored(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = new DateTime('now');
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }
}
