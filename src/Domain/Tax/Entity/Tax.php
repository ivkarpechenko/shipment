<?php

namespace App\Domain\Tax\Entity;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'tax')]
#[ORM\UniqueConstraint(
    name: 'country_id_name',
    columns: ['country_id', 'name']
)]
#[ORM\Entity(repositoryClass: TaxRepositoryInterface::class)]
class Tax
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $value;

    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $expression;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $deletedAt = null;

    public function __construct(Country $country, string $name, float $value, string $expression)
    {
        $this->country = $country;
        $this->name = $name;
        $this->value = $value;
        $this->expression = $expression;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getExpression(): string
    {
        return $this->expression;
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

    public function changeValue(float $value): void
    {
        $this->value = $value;
        $this->updatedAt = new DateTime('now');
    }

    public function changeExpression(string $expression): void
    {
        $this->expression = $expression;
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
}
