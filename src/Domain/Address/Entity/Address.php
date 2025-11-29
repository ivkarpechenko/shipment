<?php

namespace App\Domain\Address\Entity;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\ValueObject\Point;
use App\Domain\City\Entity\City;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'address')]
#[ORM\Entity(repositoryClass: AddressRepositoryInterface::class)]
#[ORM\Index(columns: ['created_at'], name: 'idx_address_created_at', options: ['where' => '(deleted_at IS NULL)'])]
class Address
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private City $city;

    #[ORM\Column(type: 'string', length: 500, unique: true, nullable: false)]
    private string $address;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $house;

    #[ORM\Column(type: 'point', nullable: true, options: [
        'comment' => 'Широта и Долгота',
    ])]
    private ?Point $point = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $flat;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $entrance;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $floor;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    private ?string $settlement = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'json', nullable: true, options: [
        'comment' => 'Исходные данные',
        'jsonb' => true,
    ])]
    /** @var string[] */
    private ?array $inputData = [];

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deletedAt = null;

    public function __construct(
        City $city,
        string $address,
        string $house,
        ?Point $point,
        ?string $postalCode,
        ?string $street,
        ?string $flat,
        ?string $entrance,
        ?string $floor,
        ?string $settlement,
        ?array $inputData
    ) {
        $this->city = $city;
        $this->address = $address;
        $this->house = $house;
        $this->point = $point;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->flat = $flat;
        $this->entrance = $entrance;
        $this->floor = $floor;
        $this->settlement = $settlement;
        $this->inputData = $inputData;

        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getHouse(): string
    {
        return $this->house;
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getFlat(): ?string
    {
        return $this->flat;
    }

    public function getEntrance(): ?string
    {
        return $this->entrance;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function getSettlement(): ?string
    {
        return $this->settlement;
    }

    public function getInputData(): ?array
    {
        return $this->inputData;
    }

    public function getInputDataBy(string $needle): iterable
    {
        $iterator = new \RecursiveArrayIterator($this->inputData);
        $recursive = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                yield $value;
            }
        }
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

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function changeHouse(string $house): void
    {
        $this->house = $house;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePoint(?Point $point): void
    {
        $this->point = $point;
        $this->updatedAt = new \DateTime('now');
    }

    public function changePostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeStreet(?string $street): void
    {
        $this->street = $street;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeFlat(?string $flat): void
    {
        $this->flat = $flat;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeEntrance(?string $entrance): void
    {
        $this->entrance = $entrance;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeFloor(?string $floor): void
    {
        $this->floor = $floor;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeSettlement(?string $settlement): void
    {
        $this->settlement = $settlement;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeInputData(?array $inputData): void
    {
        $this->inputData = $inputData;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }

    public function deleted(): void
    {
        $this->deletedAt = new \DateTime('now');
    }

    public function restored(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTime('now');
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }
}
