<?php

namespace App\Domain\Currency\Entity;

use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'currency')]
#[ORM\Entity(repositoryClass: CurrencyRepositoryInterface::class)]
#[ORM\Index(columns: ['name'], name: 'idx_currency_name', options: ['where' => '(is_active = true)'])]
#[ORM\Index(columns: ['created_at'], name: 'idx_currency_created_at', options: ['where' => '(is_active = true)'])]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 3, unique: true, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'integer', length: 3, unique: true, nullable: false)]
    private int $num;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(string $code, int $num, string $name)
    {
        $this->code = $code;
        $this->num = $num;
        $this->name = $name;
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

    public function getNum(): int
    {
        return $this->num;
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
