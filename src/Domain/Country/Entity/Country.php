<?php

namespace App\Domain\Country\Entity;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'country')]
#[ORM\Entity(repositoryClass: CountryRepositoryInterface::class)]
#[ORM\Index(columns: ['created_at'], name: 'idx_country_created_at', options: ['where' => '(deleted_at IS NULL)'])]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 250, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 2, unique: true, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $deletedAt = null;

    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
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
